<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 | Step 6 announcements + the single resident popup.
 | If an older `announcements` table exists without the Step 6 columns, it is
 | renamed to announcements_legacy and its rows are copied over (title, body/
 | content, category/tag, pinned, posted_by/author, dates). The legacy table is
 | kept; drop it once you've checked the data.
 */
return new class extends Migration
{
    public function up(): void
    {
        $legacy = null;
        if (Schema::hasTable('announcements')
            && ! Schema::hasColumns('announcements', ['category', 'title', 'body', 'pinned', 'posted_by', 'admin_id'])) {
            $legacy = 'announcements_legacy';
            Schema::dropIfExists($legacy);
            $this->dropForeignKeys('announcements');
            Schema::rename('announcements', $legacy);
        }

        if (! Schema::hasTable('announcements')) {
            Schema::create('announcements', function (Blueprint $table) {
                $table->id();
                $table->string('category', 20)->index();   // health, advisory, utility, event, assistance
                $table->string('title', 160);
                $table->text('body');
                $table->boolean('pinned')->default(false);
                $table->string('posted_by', 120);
                $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('resident_popups')) {
            Schema::create('resident_popups', function (Blueprint $table) {
                $table->id();
                $table->boolean('enabled')->default(true);
                $table->string('category', 20)->default('event');
                $table->string('title', 80);
                $table->string('body', 280);
                $table->string('image_path')->nullable();  // PUBLIC disk
                $table->foreignId('updated_by')->nullable()->constrained('admins')->nullOnDelete();
                $table->timestamps();
            });
        }

        if ($legacy) {
            $this->copyLegacy($legacy);
        }
    }

    private function copyLegacy(string $legacy): void
    {
        $cols = Schema::getColumnListing($legacy);
        $pick = function ($row, array $names) use ($cols) {
            foreach ($names as $n) {
                if (in_array($n, $cols, true) && $row->{$n} !== null && $row->{$n} !== '') {
                    return $row->{$n};
                }
            }
            return null;
        };
        $valid = array_keys(config('pabarang.announcement_categories', []));

        foreach (DB::table($legacy)->orderBy('id')->get() as $row) {
            $title = $pick($row, ['title', 'headline', 'subject']);
            if (! $title) {
                continue;
            }
            $cat = strtolower((string) $pick($row, ['category', 'tag', 'type']));
            $cat = match (true) {
                in_array($cat, $valid, true) => $cat,
                str_contains($cat, 'health') => 'health',
                str_contains($cat, 'advis')  => 'advisory',
                str_contains($cat, 'util') || str_contains($cat, 'water') => 'utility',
                str_contains($cat, 'event')  => 'event',
                str_contains($cat, 'assist') => 'assistance',
                default => 'event',
            };
            $created = $pick($row, ['created_at', 'posted_at', 'published_at']) ?? now();

            DB::table('announcements')->insert([
                'category'   => $cat,
                'title'      => mb_substr((string) $title, 0, 160),
                'body'       => (string) ($pick($row, ['body', 'content', 'details', 'message', 'description']) ?? ''),
                'pinned'     => (bool) $pick($row, ['pinned', 'is_pinned']),
                'posted_by'  => mb_substr((string) ($pick($row, ['posted_by', 'author', 'author_name']) ?? 'Barangay Alejal Office'), 0, 120),
                'created_at' => $created,
                'updated_at' => $pick($row, ['updated_at']) ?? $created,
            ]);
        }
    }

    private function dropForeignKeys(string $table): void
    {
        try {
            $fks = DB::select(
                'SELECT CONSTRAINT_NAME AS name FROM information_schema.TABLE_CONSTRAINTS
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_TYPE = ?',
                [$table, 'FOREIGN KEY']
            );
            foreach ($fks as $fk) {
                Schema::table($table, fn (Blueprint $t) => $t->dropForeign($fk->name));
            }
        } catch (\Throwable $e) {
            // not MySQL or no FKs - fine
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('resident_popups');
        Schema::dropIfExists('announcements');
        if (Schema::hasTable('announcements_legacy')) {
            Schema::rename('announcements_legacy', 'announcements');
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 | Step 6 officials.
 | The table from 2026_11_05_000000_create_pabarang_tables already has
 | name, position, committee, photo_path, sort_order. This only ADDS what
 | Step 6 needs (group, contact, term) and sorts existing rows into a group
 | based on their position. No data is removed.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('officials')) {
            Schema::create('officials', function (Blueprint $table) {
                $table->id();
                $table->string('group', 10)->default('council')->index();   // council, sk, tanod, lupon (FR-33)
                $table->string('name', 120);
                $table->string('position', 80);
                $table->string('committee', 120)->nullable();
                $table->string('contact', 30)->nullable();
                $table->string('term', 40)->nullable();
                $table->string('photo_path')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
            return;
        }

        Schema::table('officials', function (Blueprint $table) {
            if (! Schema::hasColumn('officials', 'group')) {
                $table->string('group', 10)->default('council')->index()->after('id');
            }
            if (! Schema::hasColumn('officials', 'committee')) {
                $table->string('committee')->nullable();
            }
            if (! Schema::hasColumn('officials', 'contact')) {
                $table->string('contact', 30)->nullable();
            }
            if (! Schema::hasColumn('officials', 'term')) {
                $table->string('term', 40)->nullable();
            }
            if (! Schema::hasColumn('officials', 'photo_path')) {
                $table->string('photo_path')->nullable();
            }
            if (! Schema::hasColumn('officials', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0);
            }
        });

        // Put existing officials in the right group from their position.
        foreach (DB::table('officials')->get(['id', 'position']) as $o) {
            $p = strtolower((string) $o->position);
            $group = match (true) {
                str_contains($p, 'sk ') || str_starts_with($p, 'sk') || str_contains($p, 'kabataan') => 'sk',
                str_contains($p, 'tanod') => 'tanod',
                str_contains($p, 'lupon') => 'lupon',
                default => 'council',
            };
            DB::table('officials')->where('id', $o->id)->update(['group' => $group]);
        }
    }

    public function down(): void
    {
        Schema::table('officials', function (Blueprint $table) {
            foreach (['group', 'contact', 'term'] as $col) {
                if (Schema::hasColumn('officials', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 | Step 6 document requests.
 | If the older table from 2026_11_05_000000_create_pabarang_tables exists
 | (reference_no, document_type, purpose, copies, remarks, released_at), it is
 | renamed to document_requests_legacy and every row is copied into the new
 | layout. The legacy table is kept so nothing is lost; drop it once you've
 | checked the data.
 */
return new class extends Migration
{
    public function up(): void
    {
        $legacy = null;
        if (Schema::hasTable('document_requests') && ! Schema::hasColumn('document_requests', 'details')) {
            $legacy = 'document_requests_legacy';
            Schema::dropIfExists($legacy);
            // Free the FK name so the new table can use it.
            if (Schema::hasColumn('document_requests', 'resident_id')) {
                try {
                    Schema::table('document_requests', fn (Blueprint $t) => $t->dropForeign(['resident_id']));
                } catch (\Throwable $e) {
                    // no FK - fine
                }
            }
            Schema::rename('document_requests', $legacy);
        }

        if (! Schema::hasTable('document_requests')) {
            Schema::create('document_requests', function (Blueprint $table) {
                $table->id();
                $table->string('ref', 40)->unique();                  // DOC-2026-0001
                $table->foreignId('resident_id')->nullable()->constrained('residents')->nullOnDelete();
                $table->string('type', 20)->index();                  // clearance, residency, indigency, business, vaccination, id
                $table->string('status', 20)->default('pending')->index();
                $table->string('summary', 160);
                $table->json('details');
                $table->json('files')->nullable();                    // [{label, name, path}] on the PRIVATE disk
                $table->text('note')->nullable();                     // "Note to resident"
                $table->boolean('assessed')->default(false);          // indigency assessment done
                $table->foreignId('handled_by')->nullable()->constrained('admins')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('document_request_logs')) {
            Schema::create('document_request_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('document_request_id')->constrained()->cascadeOnDelete();
                $table->string('status', 20);
                $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if ($legacy) {
            $this->copyLegacy($legacy);
        }
    }

    private function copyLegacy(string $legacy): void
    {
        $types = config('pabarang.doc_types', []);
        $cols = Schema::getColumnListing($legacy);
        $get = fn ($row, $col) => in_array($col, $cols, true) ? $row->{$col} : null;

        foreach (DB::table($legacy)->orderBy('id')->get() as $row) {
            $type = $this->mapType((string) $get($row, 'document_type'), $types);
            $status = $this->mapStatus((string) $get($row, 'status'));
            $purpose = trim((string) $get($row, 'purpose'));
            $created = $get($row, 'created_at') ?? now();
            $changed = $get($row, 'released_at') ?? $get($row, 'updated_at') ?? $created;

            $details = array_filter([
                'Purpose' => $purpose ?: null,
                'Copies'  => $get($row, 'copies') !== null ? (string) $get($row, 'copies') : null,
            ]);

            $id = DB::table('document_requests')->insertGetId([
                'ref'         => $get($row, 'reference_no') ?: ('OLD-' . $row->id),
                'resident_id' => $get($row, 'resident_id'),
                'type'        => $type,
                'status'      => $status,
                'summary'     => mb_substr($purpose ?: ($types[$type]['name'] ?? ucfirst($type)), 0, 160),
                'details'     => json_encode((object) $details),
                'files'       => json_encode([]),
                'note'        => $get($row, 'remarks'),
                'assessed'    => in_array($status, ['ready', 'completed'], true),
                'created_at'  => $created,
                'updated_at'  => $get($row, 'updated_at') ?? $created,
            ]);

            DB::table('document_request_logs')->insert(['document_request_id' => $id, 'status' => 'pending', 'created_at' => $created]);
            if ($status !== 'pending') {
                DB::table('document_request_logs')->insert(['document_request_id' => $id, 'status' => $status, 'created_at' => $changed]);
            }
        }
    }

    private function mapType(string $raw, array $types): string
    {
        $v = strtolower(trim($raw));
        if (isset($types[$v])) {
            return $v;
        }
        foreach ($types as $key => $t) {
            if ($v === strtolower($t['name'])) {
                return $key;
            }
        }
        return match (true) {
            str_contains($v, 'resid')   => 'residency',
            str_contains($v, 'indigen') => 'indigency',
            str_contains($v, 'business') => 'business',
            str_contains($v, 'vaccin')  => 'vaccination',
            str_contains($v, 'senior') || str_contains($v, 'pwd') => 'id',
            default => 'clearance',
        };
    }

    private function mapStatus(string $raw): string
    {
        $v = strtolower(trim($raw));
        return match (true) {
            in_array($v, ['pending', 'processing', 'ready', 'completed', 'rejected'], true) => $v,
            in_array($v, ['released', 'claimed', 'done', 'complete'], true) => 'completed',
            in_array($v, ['approved', 'for pickup', 'for_pickup', 'ready for pickup'], true) => 'ready',
            in_array($v, ['in progress', 'in_progress', 'progress', 'review', 'reviewing'], true) => 'processing',
            in_array($v, ['denied', 'declined', 'cancelled', 'canceled'], true) => 'rejected',
            default => 'pending',
        };
    }

    public function down(): void
    {
        Schema::dropIfExists('document_request_logs');
        Schema::dropIfExists('document_requests');
        if (Schema::hasTable('document_requests_legacy')) {
            Schema::rename('document_requests_legacy', 'document_requests');
        }
    }
};

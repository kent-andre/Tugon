<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 | Calendar: adds what the existing `events` table is missing.
 |   category                - sets the color (config pabarang.event_categories)
 |   is_public               - unticked = officials only (hidden from residents)
 |   allows_preregistration  - Health events: residents get a "Pre-register" button
 |   created_by              - the official who added it
 | Existing rows keep their data and become public "community" events.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (! Schema::hasColumn('events', 'category')) {
                $table->string('category', 20)->default('community')->index()->after('title');
            }
            if (! Schema::hasColumn('events', 'is_public')) {
                $table->boolean('is_public')->default(true)->after('all_day');
            }
            if (! Schema::hasColumn('events', 'allows_preregistration')) {
                $table->boolean('allows_preregistration')->default(false)->after('is_public');
            }
            if (! Schema::hasColumn('events', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('allows_preregistration')->constrained('admins')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'created_by')) {
                $table->dropConstrainedForeignId('created_by');
            }
            foreach (['allows_preregistration', 'is_public', 'category'] as $col) {
                if (Schema::hasColumn('events', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

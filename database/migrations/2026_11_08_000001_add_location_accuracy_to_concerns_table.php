<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// How sure the phone was about a report's location, so officials are not misled on the map.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('concerns', function (Blueprint $table) {
            if (! Schema::hasColumn('concerns', 'location_accuracy')) {
                // Meters, from the phone's GPS. Empty for older reports.
                $table->unsignedInteger('location_accuracy')->nullable()->after('longitude');
            }
            if (! Schema::hasColumn('concerns', 'location_adjusted')) {
                // True when the resident moved the pin by hand
                $table->boolean('location_adjusted')->default(false)->after('location_accuracy');
            }
        });
    }

    public function down(): void
    {
        Schema::table('concerns', function (Blueprint $table) {
            if (Schema::hasColumn('concerns', 'location_adjusted')) {
                $table->dropColumn('location_adjusted');
            }
            if (Schema::hasColumn('concerns', 'location_accuracy')) {
                $table->dropColumn('location_accuracy');
            }
        });
    }
};

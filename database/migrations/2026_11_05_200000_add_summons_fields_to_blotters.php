<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Step 4b: emails for the summons and notice of hearing, the hearing place,
// when the summons went out, and the officer's return of service.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blotters', function (Blueprint $table) {
            $table->string('complainant_email')->nullable()->after('complainant_info');
            $table->string('respondent_email')->nullable()->after('respondent_info');
            $table->string('hearing_place')->nullable()->after('hearing_at');
            $table->dateTime('summons_sent_at')->nullable()->after('hearing_place');
            $table->string('served_by')->nullable()->after('summons_sent_at');
            $table->dateTime('served_at')->nullable()->after('served_by');
            $table->string('service_method')->nullable()->after('served_at');
        });
    }

    public function down(): void
    {
        Schema::table('blotters', function (Blueprint $table) {
            $table->dropColumn([
                'complainant_email', 'respondent_email', 'hearing_place',
                'summons_sent_at', 'served_by', 'served_at', 'service_method',
            ]);
        });
    }
};

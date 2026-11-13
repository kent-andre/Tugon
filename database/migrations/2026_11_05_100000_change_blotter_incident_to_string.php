<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// The Convert to Blotter form sends the incident as text with the time
// (for example "Oct 5, 2026, 3:00 PM"), so the column cannot be a date.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blotters', function (Blueprint $table) {
            $table->string('incident')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('blotters', function (Blueprint $table) {
            $table->date('incident')->nullable()->change();
        });
    }
};

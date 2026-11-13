<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// One row per resident email that asked for a reset link. Only a hash of the
// link's secret is kept, so a leaked database can't be used to reset passwords.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resident_password_resets', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->char('token_hash', 64);
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resident_password_resets');
    }
};

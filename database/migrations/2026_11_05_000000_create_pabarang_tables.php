<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Needed if SESSION_DRIVER=database in .env
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Resident accounts. The admin is not stored here, it stays in .env.
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('purok')->nullable();
            $table->string('contact')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->timestamp('approved_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Column names match the keys in ConcernStore::row()
        Schema::create('concerns', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code')->unique();
            $table->string('category');
            $table->text('description');
            $table->string('purok');
            $table->string('reporter_name')->nullable();
            $table->string('contact')->default('');
            $table->boolean('is_anonymous')->default(false);
            // Only filled for named reports unless we decide on option B for anonymous ones
            $table->foreignId('resident_id')->nullable()->constrained('residents')->nullOnDelete();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('evidence_path')->nullable();
            $table->string('status')->default('pending'); // pending, progress, resolved, blotter
            $table->string('verification')->default('unverified'); // unverified, verified, flagged
            $table->text('flag_reason')->nullable();
            $table->string('assigned_to')->nullable();
            $table->text('internal_notes')->nullable();
            $table->text('public_update')->nullable();
            $table->string('blotter_ref')->nullable();
            $table->timestamps();
        });

        // Column names match the keys in BlotterStore::row()
        Schema::create('blotters', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('zone');
            $table->string('complainant');
            $table->string('complainant_info')->default('');
            $table->string('respondent')->default('');
            $table->string('respondent_info')->default('');
            $table->text('narrative');
            $table->date('incident')->nullable();
            $table->string('place')->default('');
            $table->string('witnesses')->default('');
            $table->string('recorded_by')->default('Barangay Secretary');
            $table->string('referred_to')->default('');
            $table->string('source_concern_code')->nullable()->index();
            $table->string('status')->default('filed'); // filed, recorded, hearing, settled
            $table->dateTime('hearing_at')->nullable();
            $table->string('mediator')->nullable();
            $table->text('settlement')->nullable();
            $table->string('cfa_no')->nullable()->unique();
            $table->timestamps();
        });

        // Columns here are a first guess, we check them at Step 6
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->foreignId('resident_id')->nullable()->constrained('residents')->nullOnDelete();
            $table->string('document_type');
            $table->string('purpose');
            $table->unsignedTinyInteger('copies')->default(1);
            $table->string('status')->default('pending'); // pending, processing, ready, released, rejected
            $table->text('remarks')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->string('category')->nullable();
            $table->timestamp('posted_at')->useCurrent();
            $table->timestamps();
        });

        // Roster only, officials do not log in
        Schema::create('officials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position');
            $table->string('committee')->nullable();
            $table->string('photo_path')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Only time, action, category and IP, nothing else
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->string('category');
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->boolean('all_day')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('officials');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('document_requests');
        Schema::dropIfExists('blotters');
        Schema::dropIfExists('concerns');
        Schema::dropIfExists('residents');
        Schema::dropIfExists('sessions');
    }
};

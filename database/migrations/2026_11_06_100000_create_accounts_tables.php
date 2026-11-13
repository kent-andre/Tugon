<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Step 5: official accounts, the full resident registration form, and the
// name of the official who did each action in the audit log.
return new class extends Migration
{
    public function up(): void
    {
        // One login per barangay official. Roles: captain, secretary, staff.
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('staff');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });

        Schema::table('residents', function (Blueprint $table) {
            $table->string('middle_name')->nullable()->after('first_name');
            $table->date('birthdate')->nullable()->after('last_name');
            $table->string('gender')->nullable()->after('birthdate');
            $table->string('civil_status')->nullable()->after('gender');
            $table->string('occupation')->nullable()->after('civil_status');
            $table->string('address')->nullable()->after('purok');
            $table->string('emergency_contact')->nullable()->after('contact');
            $table->string('id_type')->nullable()->after('emergency_contact');
            $table->string('valid_id_path')->nullable()->after('id_type');
            $table->string('resident_no')->nullable()->unique()->after('status');
            $table->string('reject_reason', 300)->nullable()->after('approved_at');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('actor')->nullable()->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', fn (Blueprint $table) => $table->dropColumn('actor'));

        Schema::table('residents', function (Blueprint $table) {
            $table->dropUnique(['resident_no']);
            $table->dropColumn([
                'middle_name', 'birthdate', 'gender', 'civil_status', 'occupation', 'address',
                'emergency_contact', 'id_type', 'valid_id_path', 'resident_no', 'reject_reason',
            ]);
        });

        Schema::dropIfExists('admins');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('concerns', function (Blueprint $table) {
            // pending | clear | flagged | error
            $table->string('photo_check_status', 20)->default('pending')->index();
            // [{code, label, severity: strong|medium|weak|info, source: basic|ai, related?}]
            $table->json('photo_flags')->nullable();
            $table->char('photo_sha256', 64)->nullable()->index();
            $table->char('photo_dhash', 16)->nullable();
            // basic | basic+ai | basic+ai_failed
            $table->string('photo_check_source', 20)->nullable();
            // Nudity or gore found by the AI check: admin view blurs it until clicked.
            $table->boolean('photo_sensitive')->default(false);
            $table->timestamp('photo_checked_at')->nullable();
            // ok | kept (set by an official from the admin Concerns page)
            $table->string('photo_review', 10)->nullable();
            $table->string('photo_reviewed_by')->nullable();
            $table->timestamp('photo_reviewed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('concerns', function (Blueprint $table) {
            $table->dropIndex(['photo_check_status']);
            $table->dropIndex(['photo_sha256']);
            $table->dropColumn([
                'photo_check_status', 'photo_flags', 'photo_sha256', 'photo_dhash',
                'photo_check_source', 'photo_sensitive', 'photo_checked_at',
                'photo_review', 'photo_reviewed_by', 'photo_reviewed_at',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('verification_codes', function (Blueprint $table) {
            // Rename code to otp_code if it exists, otherwise add it
            if (Schema::hasColumn('verification_codes', 'code')) {
                $table->renameColumn('code', 'otp_code');
            } else {
                $table->string('otp_code');
            }

            // Add target_value (identifier)
            $table->string('target_value')->after('type');

            // Add verified status
            $table->boolean('verified')->default(false)->after('expires_at');
            
            // Add attempts tracker for brute force protection
            if (!Schema::hasColumn('verification_codes', 'attempts')) {
                $table->integer('attempts')->default(0)->after('verified');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verification_codes', function (Blueprint $table) {
            $table->renameColumn('otp_code', 'code');
            $table->dropColumn(['target_value', 'verified']);
        });
    }
};

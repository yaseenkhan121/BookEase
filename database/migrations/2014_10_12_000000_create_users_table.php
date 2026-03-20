<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable(); // Nullable for OAuth users

            // OAuth
            $table->string('google_id')->nullable()->unique();
            $table->string('provider')->nullable(); // e.g. 'google'

            // Role & Status
            $table->string('role')->default('customer')->index();
            $table->string('status')->default('active')->index();

            // Profile
            $table->string('profile_image')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('theme_preference')->default('light');

            // Google Calendar Integration
            $table->text('google_calendar_token')->nullable();
            $table->string('google_calendar_refresh_token')->nullable();
            $table->string('google_calendar_email')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('users');
    }
};
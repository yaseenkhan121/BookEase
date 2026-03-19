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
            $table->string('password');
            
            /**
             * Role Management
             * Added index because we filter by 'role' in nearly every controller.
             */
            $table->string('role')->default('customer')->index(); 
            
            /**
             * Profile & Identity
             * Renamed 'profile_picture' to 'avatar' to match our User model.
             */
            $table->string('avatar')->nullable();
            $table->string('phone_number')->nullable();
            
            /**
             * Status flags
             */
            $table->boolean('is_active')->default(true);
            
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
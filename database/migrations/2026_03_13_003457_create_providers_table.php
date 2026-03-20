<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('owner_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('business_name')->nullable();
            $table->string('business_category')->nullable();
            $table->string('specialization')->nullable();
            $table->text('bio')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('logo')->nullable();
            $table->string('status')->default('pending');
            $table->boolean('setup_completed')->default(false);
            $table->boolean('is_demo')->default(false);
            $table->float('average_rating')->default(0);
            $table->integer('total_reviews')->default(0);

            // Google Calendar Integration
            $table->text('google_calendar_token')->nullable();
            $table->string('google_calendar_refresh_token')->nullable();
            $table->string('google_calendar_email')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('providers');
    }
};
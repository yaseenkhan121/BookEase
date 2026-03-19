<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This table stores in-app alerts (e.g., "New Booking Received").
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            // UUIDs are used here by default in Laravel for global uniqueness
            $table->uuid('id')->primary();

            // The class name of the notification (e.g., App\Notifications\BookingConfirmed)
            $table->string('type');

            /**
             * Morph Columns: creates 'notifiable_id' and 'notifiable_type'.
             * This allows the notification to belong to any model (User, Admin, etc).
             */
            $table->morphs('notifiable');

            // JSON data containing the message, booking ID, and action URLs
            $table->text('data');

            // Tracking if the user has seen the alert
            $table->timestamp('read_at')->nullable()->index();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
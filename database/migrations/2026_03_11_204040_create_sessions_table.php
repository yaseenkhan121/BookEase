<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Use this if you set SESSION_DRIVER=database in your .env file.
     */
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            // The unique session ID (Standard Laravel logic)
            $table->string('id')->primary();

            // Link to our 'users' table - Indexed for fast logout/session cleanup
            $table->foreignId('user_id')->nullable()->index();

            // Security Metadata
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            // The actual session data (serialized)
            $table->longText('payload');

            // Timestamp of last interaction - Indexed for garbage collection
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Required if CACHE_STORE=database or LOCK_CONNECTION=database in .env
     */
    public function up(): void
    {
        // General Data Cache
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            // Using mediumText allows for larger cached objects (like serialized provider lists)
            $table->mediumText('value');
            $table->integer('expiration')->index();
        });

        /**
         * Atomic Locks
         * Critical for Booking Systems to prevent double-booking during 
         * high-concurrency requests.
         */
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner'); // Identifies which process/request holds the lock
            $table->integer('expiration')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
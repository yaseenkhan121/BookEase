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
        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();
            
            // Unified Architecture: linked to the standalone providers table
            $table->foreignId('provider_id')->constrained('providers')->onDelete('cascade');
            
            // Schedule tracking
            $table->unsignedTinyInteger('day_of_week'); // 0 (Sunday) to 6 (Saturday)
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_available')->default(true)->index();
            
            $table->timestamps();
            
            // Ensure no overlapping schedules for the same provider on the same day/time
            $table->unique(['provider_id', 'day_of_week', 'start_time'], 'provider_day_time_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availabilities');
    }
};

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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            
            // Step 1 linkage: Link service to a specific provider
            $table->foreignId('provider_id')
                  ->constrained()
                  ->onDelete('cascade'); // If provider is deleted, services are removed
            
            $table->string('service_name');
            $table->text('description')->nullable();
            
            // Pricing logic (using decimal for precision)
            $table->decimal('price', 10, 2);
            
            // Step 7 Logic: Duration in minutes (e.g., 30, 60, 90)
            $table->integer('duration'); 
            
            // Step 2 Requirement: Ability to toggle service availability
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
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
        if (Schema::hasTable('users')) {
            if (!Schema::hasColumn('users', 'profile_image')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('profile_image')->nullable();
                });
            }
            
            // Clean up avatar if it still exists to prevent future confusion
            if (Schema::hasColumn('users', 'avatar')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropColumn('avatar');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration required for this forceful alignment
    }
};

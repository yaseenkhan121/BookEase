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
        Schema::table('users', function (Blueprint $table) {
            // Ensure profile_image exists and avatar is gone
            if (Schema::hasColumn('users', 'avatar') && !Schema::hasColumn('users', 'profile_image')) {
                $table->renameColumn('avatar', 'profile_image');
            } elseif (!Schema::hasColumn('users', 'profile_image')) {
                $table->string('profile_image')->nullable()->after('role');
            }
            
            // If both exist (e.g. from partial previous migration), drop avatar
            if (Schema::hasColumn('users', 'avatar') && Schema::hasColumn('users', 'profile_image')) {
                $table->dropColumn('avatar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'profile_image') && !Schema::hasColumn('users', 'avatar')) {
                $table->renameColumn('profile_image', 'avatar');
            }
        });
    }
};

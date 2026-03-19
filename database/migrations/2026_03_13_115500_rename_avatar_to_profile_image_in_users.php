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
            if (Schema::hasColumn('users', 'avatar') && !Schema::hasColumn('users', 'profile_image')) {
                $table->renameColumn('avatar', 'profile_image');
            } elseif (!Schema::hasColumn('users', 'profile_image')) {
                $table->string('profile_image')->nullable()->after('role');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'profile_image')) {
                $table->renameColumn('profile_image', 'avatar');
            }
        });
    }
};

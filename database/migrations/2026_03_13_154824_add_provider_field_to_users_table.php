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
            $table->string('provider')->nullable()->after('google_id');
            // If profile_image exists, we might want to rename it back to avatar to be 100% compliant with the prompt
            if (Schema::hasColumn('users', 'profile_image')) {
                $table->renameColumn('profile_image', 'avatar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('provider');
            if (Schema::hasColumn('users', 'avatar')) {
                $table->renameColumn('avatar', 'profile_image');
            }
        });
    }
};

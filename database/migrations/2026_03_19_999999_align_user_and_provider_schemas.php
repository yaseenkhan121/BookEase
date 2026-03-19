<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to align the database with the Model expectations.
     */
    public function up(): void
    {
        // 1. Fix Users Table
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // Add status column if missing (fallback for is_active)
                if (!Schema::hasColumn('users', 'status')) {
                    $table->string('status')->default('active')->after('role');
                }
                // Fix profile_image vs avatar
                if (!Schema::hasColumn('users', 'profile_image') && Schema::hasColumn('users', 'avatar')) {
                    $table->renameColumn('avatar', 'profile_image');
                } elseif (!Schema::hasColumn('users', 'profile_image')) {
                    $table->string('profile_image')->nullable()->after('role');
                }
            });
        }

        // 2. Fix Providers Table
        if (Schema::hasTable('providers')) {
            Schema::table('providers', function (Blueprint $table) {
                // Change status from Enum to String to support pending/approved/etc.
                if (Schema::hasColumn('providers', 'status')) {
                    $table->string('status')->default('pending')->change();
                } else {
                    $table->string('status')->default('pending')->after('email');
                }

                // Ensure other missing fields from model are present
                $fields = [
                    'owner_name' => 'string',
                    'business_name' => 'string',
                    'business_category' => 'string',
                    'setup_completed' => 'boolean',
                ];

                foreach ($fields as $field => $type) {
                    if (!Schema::hasColumn('providers', $field)) {
                        $table->$type($field)->nullable();
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No easy reversal for schema alignment without losing data
    }
};

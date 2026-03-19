<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. If status_new exists (from failed run), rename it to status using atomic CHANGE
        if (Schema::hasColumn('providers', 'status_new') && !Schema::hasColumn('providers', 'status')) {
            DB::statement("ALTER TABLE providers CHANGE status_new status ENUM('pending', 'approved', 'rejected', 'suspended') NOT NULL DEFAULT 'pending'");
        }

        // 2. If status exists as string, change it to Enum
        if (Schema::hasColumn('providers', 'status')) {
             // Already done by step 1 if status_new existed
             // But if we are starting fresh and 'status' is a string:
             DB::statement("ALTER TABLE providers MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'suspended') NOT NULL DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE providers MODIFY COLUMN status VARCHAR(255) DEFAULT 'pending'");
    }
};

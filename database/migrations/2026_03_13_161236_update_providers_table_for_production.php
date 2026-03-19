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
        Schema::table('providers', function (Blueprint $table) {
            $table->boolean('setup_completed')->default(false)->after('status');
            $table->boolean('is_demo')->default(false)->after('setup_completed');
        });

        // For MySQL: Update enum values to include Pending and Rejected
        DB::statement("ALTER TABLE providers MODIFY COLUMN status ENUM('Pending', 'Active', 'Inactive', 'Rejected') DEFAULT 'Pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->dropColumn(['setup_completed', 'is_demo']);
        });

        DB::statement("ALTER TABLE providers MODIFY COLUMN status ENUM('Active', 'Inactive') DEFAULT 'Active'");
    }
};

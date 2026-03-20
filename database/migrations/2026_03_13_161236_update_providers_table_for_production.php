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
            if (!Schema::hasColumn('providers', 'setup_completed')) {
                $table->boolean('setup_completed')->default(false)->after('status');
            }
            if (!Schema::hasColumn('providers', 'is_demo')) {
                $table->boolean('is_demo')->default(false)->after('setup_completed');
            }
        });

        // Drop the old constraint/type and recreate if necessary, or just skip for SQLite/PGSQL
        // Modifying ENUMs across DB systems is tricky. 
        // We'll safely change it for MySQL, and ignore for PostgreSQL since PG handles enums differently
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE providers MODIFY COLUMN status ENUM('Pending', 'Active', 'Inactive', 'Rejected') DEFAULT 'Pending'");
        } elseif (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE providers ALTER COLUMN status TYPE VARCHAR(255)");
            DB::statement("ALTER TABLE providers ALTER COLUMN status SET DEFAULT 'Pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->dropColumn(['setup_completed', 'is_demo']);
        });

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE providers MODIFY COLUMN status ENUM('Active', 'Inactive') DEFAULT 'Active'");
        } elseif (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE providers ALTER COLUMN status TYPE VARCHAR(255)");
            DB::statement("ALTER TABLE providers ALTER COLUMN status SET DEFAULT 'Active'");
        }
    }
};

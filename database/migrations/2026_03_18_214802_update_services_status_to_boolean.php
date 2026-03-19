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
        Schema::table('services', function (Blueprint $table) {
            // First, change the column to tinyint, handle data conversion if possible
            // Note: Directly changing enum to boolean in SQLite/MySQL can be tricky with Schema::table
            // We'll use a safer approach: add new column, migrate data, drop old, rename new
            $table->boolean('status_new')->default(true)->after('status');
        });

        // Migrate data: 'Active' -> 1, 'Inactive' -> 0
        DB::table('services')->where('status', 'Active')->update(['status_new' => 1]);
        DB::table('services')->where('status', 'Inactive')->update(['status_new' => 0]);
        // Handle lowercase versions just in case
        DB::table('services')->where('status', 'active')->update(['status_new' => 1]);
        DB::table('services')->where('status', 'inactive')->update(['status_new' => 0]);

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->renameColumn('status_new', 'status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->enum('status_old', ['Active', 'Inactive'])->default('Active')->after('status');
        });

        DB::table('services')->where('status', 1)->update(['status_old' => 'Active']);
        DB::table('services')->where('status', 0)->update(['status_old' => 'Inactive']);

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->renameColumn('status_old', 'status');
        });
    }
};

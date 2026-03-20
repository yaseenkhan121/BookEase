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
        if (\Illuminate\Support\Facades\DB::connection()->getDriverName() === 'pgsql') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE providers ALTER COLUMN status TYPE VARCHAR(255)");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE providers ALTER COLUMN status SET DEFAULT 'pending'");
        } else {
            Schema::table('providers', function (Blueprint $table) {
                $table->string('status')->default('pending')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (\Illuminate\Support\Facades\DB::connection()->getDriverName() === 'pgsql') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE providers ALTER COLUMN status TYPE VARCHAR(255)");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE providers ALTER COLUMN status SET DEFAULT 'Pending'");
        } else {
            Schema::table('providers', function (Blueprint $table) {
                $table->string('status')->default('Pending')->change();
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to support passwordless OAuth users.
     */
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            if (\Illuminate\Support\Facades\DB::connection()->getDriverName() === 'mysql') {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE users MODIFY password VARCHAR(255) NULL');
            } elseif (\Illuminate\Support\Facades\DB::connection()->getDriverName() === 'pgsql') {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE users ALTER COLUMN password DROP NOT NULL');
            } else {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('password')->nullable()->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            if (\Illuminate\Support\Facades\DB::connection()->getDriverName() === 'mysql') {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL');
            } elseif (\Illuminate\Support\Facades\DB::connection()->getDriverName() === 'pgsql') {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE users ALTER COLUMN password SET NOT NULL');
            } else {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('password')->nullable(false)->change();
                });
            }
        }
    }
};

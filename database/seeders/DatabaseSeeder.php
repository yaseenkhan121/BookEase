<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use App\Models\Provider;
use App\Models\Availability;
use App\Models\Appointment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create System Admin (Bypass Eloquent for absolute certainty)
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@bookease.com'],
            [
                'name'          => 'BookEase Admin',
                'password'      => Hash::make('Admin@2026'),
                'role'          => 'admin',
                'status'        => 'active',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]
        );
    }
}
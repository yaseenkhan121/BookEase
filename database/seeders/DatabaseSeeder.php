<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use App\Models\Provider;
use App\Models\Availability;
use App\Models\Appointment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create System Admin
        User::updateOrCreate(
            ['email' => 'admin@bookease.com'],
            [
                'name'          => 'BookEase Admin',
                'password'      => 'Admin@2026', // Plain string because User model has 'hashed' cast
                'role'          => User::ROLE_ADMIN,
                'status'        => User::STATUS_ACTIVE,
                'is_active'     => true,
            ]
        );
    }
}
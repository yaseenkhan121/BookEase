<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrator', 
                'slug' => 'admin',
                'description' => 'Full system access and user management.'
            ],
            [
                'name' => 'Service Provider', 
                'slug' => 'provider',
                'description' => 'Can manage services, availability, and bookings.'
            ],
            [
                'name' => 'Customer', 
                'slug' => 'customer',
                'description' => 'Can browse providers and book appointments.'
            ],
        ];

        /*
        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']], // Search by slug
                [
                    'name' => $role['name'],
                    'description' => $role['description']
                ]
            );
        }
        */
    }
}
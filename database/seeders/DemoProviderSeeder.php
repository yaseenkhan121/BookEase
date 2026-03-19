<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Provider;
use App\Models\Service;
use App\Models\Availability;
use App\Models\User;

class DemoProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provider = Provider::where('business_name', "meer ahmad's Business")->first();

        if (!$provider) {
            return;
        }

        // 1. Add a Default Service if none exist
        if ($provider->services()->count() === 0) {
            Service::create([
                'provider_id'      => $provider->id,
                'name'             => 'Consultancy Session',
                'description'      => 'A professional consultation session for business optimization.',
                'price'            => 1500.00,
                'duration_minutes' => 60,
                'status'           => 1, // Active
            ]);
            Service::create([
                'provider_id'      => $provider->id,
                'name'             => 'Strategy Planning',
                'description'      => 'In-depth strategic planning for long-term goals.',
                'price'            => 3000.00,
                'duration_minutes' => 120,
                'status'           => 1, // Active
            ]);
        }

        // 2. Add Availabilities if none exist (Mon-Fri 9AM-5PM)
        if ($provider->availabilities()->count() === 0) {
            for ($day = 1; $day <= 5; $day++) {
                Availability::create([
                    'provider_id' => $provider->id,
                    'day_of_week' => $day,
                    'start_time'  => '09:00:00',
                    'end_time'    => '17:00:00',
                    'is_available' => true,
                ]);
            }
        }

        // 3. Ensure setup_completed is true
        $provider->checkSetupCompletion();
    }
}

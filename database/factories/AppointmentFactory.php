<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        // 1. Fetch a random service or create one if none exists
        $service = Service::inRandomOrder()->first() ?? Service::factory()->create();
        
        // 2. Generate a logical Start Time (Future: Next 14 days)
        $startDate = Carbon::now()
            ->addDays(rand(1, 14))
            ->setHour(rand(9, 17)) 
            ->setMinute(collect([0, 30])->random())
            ->setSecond(0);

        // 3. End Time based on Service Duration
        // Note: Ensure your Service model has 'duration_minutes'
        $duration = $service->duration_minutes ?? 60;
        $endDate = $startDate->copy()->addMinutes($duration);

        return [
            'customer_id' => User::where('role', 'customer')->inRandomOrder()->first()?->id ?? User::factory()->state(['role' => 'customer']),
            'provider_id' => $service->provider_id ?? User::factory()->state(['role' => 'provider']),
            'service_id'  => $service->id,
            'start_time'  => $startDate,
            'end_time'    => $endDate,
            'status'      => 'pending',
            'notes'       => $this->faker->boolean(70) ? $this->faker->sentence() : null,
        ];
    }

    /**
     * State: Appointment is already finished (Past Date).
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $start = Carbon::now()->subDays(rand(1, 15))->setHour(rand(9, 15))->setMinute(0);
            return [
                'status'     => 'completed',
                'start_time' => $start,
                'end_time'   => $start->copy()->addHour(),
            ];
        });
    }

    /**
     * State: Appointment is upcoming and confirmed.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    /**
     * State: Appointment was cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    /**
     * State: Appointment is for today.
     */
    public function today(): static
    {
        return $this->state(function (array $attributes) {
            $start = Carbon::now()->setHour(rand(9, 16))->setMinute(30);
            return [
                'start_time' => $start,
                'end_time'   => $start->copy()->addHour(),
            ];
        });
    }
}
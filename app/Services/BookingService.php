<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Availability;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;

class BookingService
{
    /**
     * Check if a provider's time slot is available (no overlapping active bookings).
     * Uses the overlap formula: (Start A < End B) AND (End A > Start B)
     */
    public function isSlotAvailable(int $providerId, $startTime, $endTime): bool
    {
        $startTime = Carbon::parse($startTime);
        $endTime = Carbon::parse($endTime);

        return !Booking::where('provider_id', $providerId)
            ->active() // Excludes cancelled bookings
            /** @var \Illuminate\Database\Query\Builder $query */
            ->whereNested(function (Builder $query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
            })
            ->exists();
    }

    /**
     * Generate available time slots for a provider on a specific date,
     * based on their availability schedule and existing bookings.
     *
     * Step 4 of the booking flow:
     * 1. Fetch provider_availability for the day
     * 2. Subtract existing bookings
     * 3. Generate slots based on service duration_minutes
     */
    public function getAvailableSlots(int $providerId, string $date, int $durationMinutes): array
    {
        $requestedDate = Carbon::parse($date);

        // 1. Fetch working hours for the specific day of the week (0=Sun, 6=Sat)
        $workingHours = Availability::where('provider_id', $providerId)
            ->where('day_of_week', $requestedDate->dayOfWeek)
            ->where('is_available', true)
            ->get();

        if ($workingHours->isEmpty()) {
            return [];
        }

        // 2. Fetch all existing active bookings for this day (single query, in-memory filter)
        $existingBookings = Booking::where('provider_id', $providerId)
            ->whereDate('start_time', $date)
            ->active()
            ->get(['start_time', 'end_time']);

        $availableSlots = [];

        foreach ($workingHours as $schedule) {
            $startStr = $this->formatTime($schedule->start_time);
            $endStr = $this->formatTime($schedule->end_time);

            $shiftStart = Carbon::parse($date . ' ' . $startStr);
            $shiftEnd   = Carbon::parse($date . ' ' . $endStr);

            // 3. Generate time slots at intervals matching service duration
            $period = CarbonPeriod::since($shiftStart)
                ->minutes($durationMinutes)
                ->until($shiftEnd->copy()->subMinutes($durationMinutes));

            foreach ($period as $slot) {
                $slotStart = $slot;
                $slotEnd   = $slot->copy()->addMinutes($durationMinutes);

                // Filter: Must not be in the past
                if ($slotStart->isPast()) {
                    continue;
                }

                // Filter: Must not overlap with existing bookings
                $isBooked = $existingBookings->contains(function ($booking) use ($slotStart, $slotEnd) {
                    $bookStart = Carbon::parse($booking->start_time);
                    $bookEnd = Carbon::parse($booking->end_time);
                    return $bookStart < $slotEnd && $bookEnd > $slotStart;
                });

                if (!$isBooked) {
                    $availableSlots[] = [
                        'time'      => $slotStart->format('g:i A'),
                        'raw_time'  => $slotStart->format('H:i'),
                        'timestamp' => $slotStart->toDateTimeString(),
                    ];
                }
            }
        }

        return $availableSlots;
    }

    /**
     * Calculate completion rate for provider dashboard.
     */
    public function getCompletionRate(int $providerId): int
    {
        $stats = Booking::where('provider_id', $providerId)
            ->selectRaw('count(*) as total')
            ->selectRaw("count(case when status = 'completed' then 1 end) as completed")
            ->first();

        if (!$stats || $stats->total === 0) {
            return 0;
        }

        return (int) round(($stats->completed / $stats->total) * 100);
    }

    /**
     * Helper to ensure time is returned as a string.
     */
    private function formatTime($time): string
    {
        if ($time instanceof Carbon) {
            return $time->format('H:i:s');
        }

        return $time ? (string) $time : '00:00:00';
    }
}
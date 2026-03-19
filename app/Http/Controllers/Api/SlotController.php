<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Service;
use Carbon\Carbon;

class SlotController extends Controller
{
    /**
     * Fetch available time slots for a specific provider and date.
     */
    public function getSlots(Request $request)
    {
        $request->validate([
            'provider_id' => 'required|exists:providers,id',
            'service_id'  => 'required|exists:services,id',
            'date'        => 'required|date|after_or_equal:today',
        ]);

        $service = Service::findOrFail($request->service_id);
        $duration = $service->duration_minutes;

        $dayOfWeek = Carbon::parse($request->date)->dayOfWeek;

        $availability = \App\Models\Availability::where('provider_id', $request->provider_id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->first();

        if (!$availability) {
            return response()->json([]);
        }

        $startTime = Carbon::parse($request->date . ' ' . $availability->start_time);
        $endTime = Carbon::parse($request->date . ' ' . $availability->end_time);

        $existingBookings = Booking::where('provider_id', $request->provider_id)
            ->whereDate('start_time', $request->date)
            ->active()
            ->get(['start_time', 'end_time']);

        $availableSlots = [];

        while ($startTime->copy()->addMinutes($duration)->lte($endTime)) {
            $slotStart = $startTime->copy();
            $slotEnd = $startTime->copy()->addMinutes($duration);

            $isOverlap = $existingBookings->contains(function ($booking) use ($slotStart, $slotEnd) {
                return ($slotStart->lt(Carbon::parse($booking->end_time)) && $slotEnd->gt(Carbon::parse($booking->start_time)));
            });

            if (!$isOverlap && $slotStart->gt(now())) {
                $availableSlots[] = [
                    'time'     => $slotStart->format('g:i A'),
                    'raw_time' => $slotStart->format('H:i:s'),
                ];
            }

            $startTime->addMinutes(30);
        }

        return response()->json($availableSlots);
    }
}
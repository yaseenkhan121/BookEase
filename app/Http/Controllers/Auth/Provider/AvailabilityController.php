<?php

namespace App\Http\Controllers\Auth\Provider; // Keeping your specific namespace

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AvailabilityController extends Controller
{
    /**
     * Display the provider's weekly schedule settings.
     */
    public function index(): View
    {
        // Get the provider profile associated with the logged-in user
        // Using the relationship we established in the User model
        $provider = Provider::where('email', Auth::user()->email)->firstOrFail();

        $availabilities = $provider->availabilities()
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('provider.availability.index', compact('availabilities', 'provider'));
    }

    /**
     * Store a new availability slot.
     * Implements Step 3: Provider must set working schedule.
     */
    public function store(Request $request): RedirectResponse
    {
        $provider = Provider::where('email', Auth::user()->email)->firstOrFail();

        // 1. Validation (Matches Step 3: Monday 09:00 AM - 05:00 PM example)
        $request->validate([
            'day_of_week' => ['required', 'integer', 'min:0', 'max:6'],
            'start_time'  => ['required', 'date_format:H:i'],
            'end_time'    => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        // 2. Senior Logic: Check for overlapping slots using the scope in Availability model
        $exists = $provider->availabilities()
            ->where('day_of_week', $request->day_of_week)
            ->overlapping($request->start_time, $request->end_time)
            ->exists();

        if ($exists) {
            return back()->with('error', 'This time slot overlaps with an existing schedule.');
        }

        // 3. Create Slot linked to Provider ID
        $provider->availabilities()->create([
            'day_of_week'  => $request->day_of_week,
            'start_time'   => $request->start_time,
            'end_time'     => $request->end_time,
            'is_available' => true,
        ]);

        $provider->checkSetupCompletion();

        return back()->with('success', 'Working hours added successfully!');
    }

    /**
     * Remove an availability slot.
     */
    public function destroy(Availability $availability): RedirectResponse
    {
        $provider = Provider::where('email', Auth::user()->email)->firstOrFail();

        // Security check: Ensure the provider owns this specific slot
        if ($availability->provider_id !== $provider->id) {
            abort(403, 'Unauthorized action.');
        }

        $availability->delete();

        $provider->checkSetupCompletion();

        return back()->with('success', 'Time slot removed.');
    }
}
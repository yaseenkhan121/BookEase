<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use App\Models\Provider;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $service)
    {
        $this->bookingService = $service;
    }

    /**
     * Display a listing of bookings based on user role.
     */
    public function index(): View
    {
        $user = Auth::user();

        $query = Booking::with(['service', 'provider', 'customer']);

        if ($user->role === 'provider') {
            $query->where('provider_id', $user->providerProfile->id);
        } elseif ($user->role === 'customer') {
            $query->where('customer_id', $user->id);
        }

        $appointments = $query->latest('start_time')->paginate(10);

        return view('bookings.index', compact('appointments'));
    }

    /**
     * Display the specified booking details.
     */
    public function show(Booking $appointment): View
    {
        $user = Auth::user();

        // Security: Ensure user only sees their own bookings (unless Admin)
        if (!$user->isAdmin()) {
            if ($user->isProvider() && $user->providerProfile->id !== $appointment->provider_id) {
                abort(403);
            }
            if ($user->isCustomer() && $user->id !== $appointment->customer_id) {
                abort(403);
            }
        }

        $appointment->load(['service', 'provider', 'customer']);

        return view('bookings.show', compact('appointment'));
    }

    /**
     * Show the new booking flow.
     */
    public function newFlow(): View
    {
        $providers = Provider::approved()->get();
        return view('bookings.new', compact('providers'));
    }

    /**
     * Show the booking form for a specific provider and service.
     */
    public function create(Provider $provider, Service $service): View
    {
        if ($service->provider_id !== $provider->id) {
            abort(404, 'This service is not offered by this provider.');
        }

        return view('bookings.create', compact('provider', 'service'));
    }

    /**
     * AJAX: Get available time slots for a provider on a specific date.
     * Step 4 of the booking flow.
     */
    public function getAvailableSlots(Request $request): JsonResponse
    {
        $request->validate([
            'provider_id' => 'required|exists:providers,id',
            'service_id'  => 'required|exists:services,id',
            'date'        => 'required|date|after_or_equal:today',
        ]);

        $service = Service::findOrFail($request->service_id);

        $slots = $this->bookingService->getAvailableSlots(
            $request->provider_id,
            $request->date,
            $service->duration_minutes
        );

        return response()->json([
            'slots' => $slots,
            'service_duration' => $service->duration_minutes,
        ]);
    }

    /**
     * Display the calendar view.
     */
    public function calendar(): View
    {
        $user = Auth::user();

        $bookings = Booking::with(['service', 'customer', 'provider'])
            ->whereNested(function ($query) use ($user) {
                if ($user->role === 'provider' && $user->providerProfile) {
                    $query->where('provider_id', $user->providerProfile->id);
                } elseif ($user->role === 'customer') {
                    $query->where('customer_id', $user->id);
                }
            })
            ->get();

        $events = $bookings->map(fn($b) => [
            'id'    => $b->id,
            'title' => ($b->service->name ?? 'Service') . " - " . ($b->customer->name ?? 'User'),
            'start' => Carbon::parse($b->start_time)->toIso8601String(),
            'end'   => Carbon::parse($b->end_time)->toIso8601String(),
            'backgroundColor' => $this->getStatusColor($b->status),
            'borderColor'     => $this->getStatusColor($b->status),
            'extendedProps' => [
                'status'   => ucfirst($b->status),
                'customer' => $b->customer->name ?? 'N/A',
                'service'  => $b->service->name ?? 'N/A',
            ],
        ]);

        return view('calendar.index', compact('events'));
    }

    /**
     * Handle the creation of a new booking.
     * Step 5: Double-booking prevention with DB transaction.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'service_id'     => ['required', 'exists:services,id'],
            'provider_id'    => ['required', 'exists:providers,id'],
            'date'           => ['required', 'date', 'after_or_equal:today'],
            'time'           => ['required'],
            'customer_name'  => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $service = Service::findOrFail($request->service_id);
            $startTime = Carbon::parse($request->date . ' ' . $request->time);
            $endTime = $startTime->copy()->addMinutes($service->duration_minutes);

            // Verify provider is approved
            $provider = Provider::findOrFail($request->provider_id);
            if ($provider->status !== Provider::STATUS_APPROVED) {
                return back()->withInput()->with('error', 'This provider is not currently accepting bookings.');
            }

            $booking = DB::transaction(function () use ($request, $service, $startTime, $endTime) {
                // Double-booking prevention: atomic check
                if (!$this->bookingService->isSlotAvailable($request->provider_id, $startTime, $endTime)) {
                    throw new \Exception('The selected time slot is no longer available. Please choose another time.');
                }

                return Booking::create([
                    'customer_id'    => Auth::id(),
                    'customer_name'  => $request->customer_name,
                    'customer_phone' => $request->customer_phone,
                    'provider_id'    => $request->provider_id,
                    'service_id'     => $service->id,
                    'start_time'     => $startTime,
                    'end_time'       => $endTime,
                    'status'         => Booking::STATUS_PENDING,
                    'notes'          => $request->notes,
                    'price'          => $service->price,
                ]);
            });

            // Step 6: Notify provider
            if ($booking->provider && $booking->provider->user) {
                $booking->provider->user->notify(new \App\Notifications\BookingNotification(
                    'New Booking Request',
                    "You have a new booking from {$request->customer_name} for {$service->name}.",
                    route('bookings.index')
                ));
            }

            return redirect()->route('bookings.index')->with('success', 'Your booking request has been sent!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Update the status of a booking (Provider/Admin action).
     * Step 7 & 8: Status change with customer notification.
     */
    public function updateStatus(Request $request, Booking $appointment): RedirectResponse|JsonResponse
    {
        if (Auth::user()->isProvider() && Auth::user()->providerProfile && Auth::user()->providerProfile->id !== $appointment->provider_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:confirmed,rejected,completed,cancelled,in_progress'],
        ]);

        $appointment->update(['status' => $validated['status']]);

        // Step 10: Sync to Google Calendar on confirmation (Background Job)
        if ($validated['status'] === 'confirmed') {
            \App\Jobs\SyncGoogleCalendarEvent::dispatch($appointment);
        }

        // Remove from Google Calendar if rejected or cancelled (Background Job)
        if (in_array($validated['status'], ['rejected', 'cancelled'])) {
            \App\Jobs\DeleteGoogleCalendarEvent::dispatch($appointment);
        }

        // Step 8: Notify customer
        if ($appointment->customer) {
            $statusLabel = ucfirst($validated['status']);
            $appointment->customer->notify(new \App\Notifications\BookingNotification(
                "Booking {$statusLabel}",
                "Your booking for {$appointment->service->name} has been {$validated['status']}.",
                route('bookings.index')
            ));
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Booking marked as ' . ucfirst($validated['status']) . '.']);
        }

        return back()->with('success', "Booking marked as " . ucfirst($validated['status']) . ".");
    }

    /**
     * Cancel a booking (Customer side).
     */
    public function destroy(Booking $appointment): RedirectResponse
    {
        $user = Auth::user();
        if ($user->id !== $appointment->customer_id && !$user->isAdmin()) {
            abort(403);
        }

        $appointment->update(['status' => Booking::STATUS_CANCELLED]);
        
        // Remove from Google Calendar (Background Job)
        \App\Jobs\DeleteGoogleCalendarEvent::dispatch($appointment);
        if ($appointment->provider && $appointment->provider->user) {
            $appointment->provider->user->notify(new \App\Notifications\BookingNotification(
                'Booking Cancelled',
                "The booking for {$appointment->service->name} with {$appointment->customer->name} has been cancelled.",
                route('bookings.index')
            ));
        }

        return back()->with('success', 'Booking cancelled successfully.');
    }

    /**
     * Reschedule an existing booking (Customer side).
     * Step 9: Validate old vs new slot and notify provider.
     */
    public function reschedule(Request $request, Booking $appointment): RedirectResponse
    {
        // 1. Security Check
        if ($appointment->customer_id !== Auth::id()) {
            abort(403);
        }

        // 2. Business Constraint: Cannot reschedule past appointments
        if (Carbon::parse($appointment->start_time)->isPast()) {
            return back()->with('error', 'You cannot reschedule an appointment that has already started or passed.');
        }

        // 3. Validation
        $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required'],
        ]);

        try {
            $newStartTime = Carbon::parse($request->date . ' ' . $request->time);
            $duration = $appointment->service->duration_minutes;
            $newEndTime = $newStartTime->copy()->addMinutes($duration);

            // 4. Double-booking prevention
            // We must temporarily ignore the CURRENT booking record to avoid "overlapping with itself"
            /** @var \Illuminate\Database\Query\Builder $availabilityCheck */
            $isAvailable = !Booking::where('provider_id', $appointment->provider_id)
                ->where('id', '!=', $appointment->id)
                ->active()
                ->whereNested(function ($query) use ($newStartTime, $newEndTime) {
                    $query->where(function ($q) use ($newStartTime, $newEndTime) {
                        $q->where('start_time', '<', $newEndTime)
                            ->where('end_time', '>', $newStartTime);
                    });
                })->exists();

            if (!$isAvailable) {
                return back()->with('error', 'The selected slot is already booked. Please choose another time.');
            }

            // 5. Update Record
            $oldTime = Carbon::parse($appointment->start_time)->format('M d, g:i A');
            $appointment->update([
                'start_time' => $newStartTime,
                'end_time'   => $newEndTime,
                'status'     => Booking::STATUS_PENDING,
            ]);

            // 6. Notify Provider
            if ($appointment->provider && $appointment->provider->user) {
                $appointment->provider->user->notify(new \App\Notifications\BookingNotification(
                    'Appointment Rescheduled',
                    "Customer {$appointment->customer_name} rescheduled their appointment from {$oldTime} to {$newStartTime->format('M d, g:i A')}.",
                    route('provider.bookings.index')
                ));
            }

            return back()->with('success', 'Appointment rescheduled successfully! Waiting for provider confirmation.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reschedule: ' . $e->getMessage());
        }
    }

    /**
     * Helper: Calendar event color based on status.
     */
    private function getStatusColor($status): string
    {
        return match ($status) {
            'confirmed'   => '#10B981',
            'pending'     => '#F59E0B',
            'rejected'    => '#EF4444',
            'completed'   => '#3B82F6',
            'cancelled'   => '#94A3B8',
            'in_progress' => '#6366F1',
            default       => '#64748B',
        };
    }
}
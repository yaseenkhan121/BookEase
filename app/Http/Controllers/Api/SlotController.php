<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Service;
use App\Services\AppointmentService;
use Carbon\Carbon;

class SlotController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $service)
    {
        $this->appointmentService = $service;
    }

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

        $availableSlots = $this->appointmentService->getAvailableSlots(
            $request->provider_id,
            $request->date,
            $service->duration
        );

        return response()->json($availableSlots);
    }
}
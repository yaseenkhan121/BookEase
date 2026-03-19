<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Services\GoogleCalendarService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteGoogleCalendarEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $booking;

    /**
     * Create a new job instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Execute the job.
     */
    public function handle(GoogleCalendarService $calendarService): void
    {
        // 1. Remove from Provider's Calendar
        $calendarService->deleteEvent($this->booking);

        // 2. Remove from Customer's Calendar
        $calendarService->deleteEventForCustomer($this->booking);
    }
}

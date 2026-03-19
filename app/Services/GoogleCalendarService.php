<?php

namespace App\Services;

use App\Models\User;
use App\Models\Provider;
use App\Models\Booking;
use Google\Client as Google_Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    private $client;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->addScope(Calendar::CALENDAR_EVENTS);
    }

    /**
     * Initializes the client with the provider's tokens.
     * Refreshes the token if it's expired.
     */
    private function authenticateProvider(Provider $provider)
    {
        if (!$provider->google_calendar_token) {
            return false;
        }

        $this->client->setAccessToken($provider->google_calendar_token);

        // Check if token is expired
        if ($this->client->isAccessTokenExpired()) {
            if ($provider->google_calendar_refresh_token) {
                // Fetch new access token
                $newToken = $this->client->fetchAccessTokenWithRefreshToken($provider->google_calendar_refresh_token);
                
                // Merge refresh token back into the array if it wasn't returned
                if (!isset($newToken['refresh_token'])) {
                    $newToken['refresh_token'] = $provider->google_calendar_refresh_token;
                }

                // Save updated token
                $provider->google_calendar_token = $newToken;
                $provider->save();
            } else {
                // Cannot refresh, connection is broken
                return false;
            }
        }

        return true;
    }

    /**
     * Initializes the client with the user's tokens (for Customers).
     */
    private function authenticateCustomer(User $user)
    {
        if (!$user->google_calendar_token) {
            return false;
        }

        $this->client->setAccessToken($user->google_calendar_token);

        if ($this->client->isAccessTokenExpired()) {
            if ($user->google_calendar_refresh_token) {
                $newToken = $this->client->fetchAccessTokenWithRefreshToken($user->google_calendar_refresh_token);
                
                if (!isset($newToken['refresh_token'])) {
                    $newToken['refresh_token'] = $user->google_calendar_refresh_token;
                }

                $user->google_calendar_token = $newToken;
                $user->save();
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Creates an event on the Provider's Google Calendar
     */
    public function createEvent(Booking $booking)
    {
        // Load relationships
        $booking->loadMissing(['customer', 'service', 'provider']);
        
        $provider = $booking->provider;

        if (!$this->authenticateProvider($provider)) {
            return null; // Not connected or auth failed
        }

        $service = new Calendar($this->client);
        $event = new Event([
            'summary' => $booking->service->name . ' with ' . $booking->customer->name,
            'description' => "Booking Details:\n\n" .
                             "Customer: " . $booking->customer->name . "\n" .
                             "Service: " . $booking->service->name . "\n" .
                             "Email: " . $booking->customer->email . "\n" .
                             "Notes: " . ($booking->notes ?? 'None'),
            'start' => [
                'dateTime' => \Carbon\Carbon::parse($booking->start_time)->toRfc3339String(),
                'timeZone' => config('app.timezone'),
            ],
            'end' => [
                'dateTime' => \Carbon\Carbon::parse($booking->end_time)->toRfc3339String(),
                'timeZone' => config('app.timezone'),
            ],
            'attendees' => [
                ['email' => $booking->customer->email],
                ['email' => $provider->google_calendar_email],
            ],
            'reminders' => [
                'useDefault' => FALSE,
                'overrides' => [
                    ['method' => 'email', 'minutes' => 24 * 60],
                    ['method' => 'popup', 'minutes' => 30],
                ],
            ],
        ]);

        try {
            $calendarId = 'primary';
            $event = $service->events->insert($calendarId, $event);
            
            // Link the google event ID to our booking
            $booking->update([
                'google_event_id' => $event->getId()
            ]);

            return $event;
            
        } catch (\Exception $e) {
            Log::error('Google Calendar Event Creation Failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Deletes an event from the Provider's Google Calendar if the booking is cancelled
     */
    public function deleteEvent(Booking $booking)
    {
        if (!$booking->google_event_id) {
            return;
        }

        $provider = $booking->provider;

        if (!$this->authenticateProvider($provider)) {
            return;
        }

        $service = new Calendar($this->client);

        try {
            $service->events->delete('primary', $booking->google_event_id);
            $booking->update(['google_event_id' => null]);
        } catch (\Exception $e) {
            Log::error('Google Calendar Event Deletion Failed: ' . $e->getMessage());
        }
    }

    /**
     * Creates an event specifically on the Customer's connected Google Calendar.
     * The provider method links it to `google_event_id`. For the customer, we can link it
     * to `customer_google_event_id` or just skip linking since customers are the ones receiving the service.
     * But to keep it simple, we just create the event.
     */
    public function createEventForCustomer(Booking $booking)
    {
        $booking->loadMissing(['customer', 'service', 'provider']);
        
        $customer = $booking->customer;

        if (!$customer || !$this->authenticateCustomer($customer)) {
            return null;
        }

        $service = new Calendar($this->client);
        $event = new Event([
            'summary' => $booking->service->name . ' with ' . ($booking->provider->business_name ?? $booking->provider->name),
            'description' => "Booking Details:\n\n" .
                             "Provider: " . ($booking->provider->business_name ?? $booking->provider->name) . "\n" .
                             "Service: " . $booking->service->name . "\n" .
                             "Notes: " . ($booking->notes ?? 'None'),
            'start' => [
                'dateTime' => \Carbon\Carbon::parse($booking->start_time)->toRfc3339String(),
                'timeZone' => config('app.timezone'),
            ],
            'end' => [
                'dateTime' => \Carbon\Carbon::parse($booking->end_time)->toRfc3339String(),
                'timeZone' => config('app.timezone'),
            ],
            'reminders' => [
                'useDefault' => TRUE,
            ],
        ]);

        try {
            $event = $service->events->insert('primary', $event);
            
            // We can optionally store this if added to the Database (customer_google_event_id)
            $booking->update(['customer_google_event_id' => $event->getId()]);
            return $event;
            
        } catch (\Exception $e) {
            Log::error('Customer Google Calendar Sync Failed: ' . $e->getMessage());
            return null;
        }
    }

    public function deleteEventForCustomer(Booking $booking)
    {
        if (!$booking->customer_google_event_id) {
            return;
        }

        $customer = $booking->customer;

        if (!$customer || !$this->authenticateCustomer($customer)) {
            return;
        }

        $service = new Calendar($this->client);

        try {
            $service->events->delete('primary', $booking->customer_google_event_id);
            $booking->update(['customer_google_event_id' => null]);
        } catch (\Exception $e) {
            Log::error('Customer Google Calendar Deletion Failed: ' . $e->getMessage());
        }
    }
}

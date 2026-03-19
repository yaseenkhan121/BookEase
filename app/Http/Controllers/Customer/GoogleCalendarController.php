<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Google\Client as Google_Client;

class GoogleCalendarController extends Controller
{
    /**
     * Redirects the customer to Google to authorize Calendar access
     */
    public function connect()
    {
        $client = $this->getClient();
        $authUrl = $client->createAuthUrl();

        return redirect()->away($authUrl);
    }

    /**
     * Handles the OAuth response and saves tokens to the user
     */
    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('calendar')
                             ->with('error', 'Google Calendar connection was cancelled.');
        }

        if (!$request->has('code')) {
            return redirect()->route('calendar')
                             ->with('error', 'Authorization code not received.');
        }

        $client = $this->getClient();
        $token = $client->fetchAccessTokenWithAuthCode($request->code);

        if (array_key_exists('error', $token)) {
            return redirect()->route('calendar')
                             ->with('error', 'Error fetching access token: ' . $token['error']);
        }

        $user = Auth::user();

        // Save tokens
        $user->google_calendar_token = $token;
        
        // Google only returns the refresh token the FIRST time the user approves offline access.
        if (isset($token['refresh_token'])) {
            $user->google_calendar_refresh_token = $token['refresh_token'];
        }

        // Fetch user email from Google to display in the UI
        $oauth2 = new \Google\Service\Oauth2($client);
        $userInfo = $oauth2->userinfo->get();
        $user->google_calendar_email = $userInfo->email;

        $user->save();

        return redirect()->route('calendar')
                         ->with('success', 'Google Calendar connected successfully!');
    }

    /**
     * Disconnects the Google Calendar integration
     */
    public function disconnect()
    {
        $user = Auth::user();
        
        // Optional: revoke token on Google's side
        if ($user->google_calendar_token) {
            $client = $this->getClient();
            try {
                $client->revokeToken($user->google_calendar_token);
            } catch (\Exception $e) {
                // Ignore errors if token is already expired
            }
        }

        // Clear tokens from DB
        $user->google_calendar_token = null;
        $user->google_calendar_refresh_token = null;
        $user->google_calendar_email = null;
        $user->save();

        return redirect()->route('calendar')
                         ->with('success', 'Google Calendar disconnected.');
    }

    /**
     * Helper to configure the Google Client specifically for Customer Calendar sync
     */
    private function getClient()
    {
        $client = new Google_Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        
        // Explicit redirect URI for customer calendar sync
        $redirectUri = route('customer.calendar.callback');
        $client->setRedirectUri($redirectUri);

        // Request calendar events access & user email
        // We only need calendar.events.freebusy or calendar.events to sync.
        $client->addScope('https://www.googleapis.com/auth/calendar.events');
        $client->addScope('https://www.googleapis.com/auth/userinfo.email');

        $client->setAccessType('offline');
        $client->setPrompt('consent');

        return $client;
    }
}

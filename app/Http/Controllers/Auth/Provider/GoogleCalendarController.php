<?php

namespace App\Http\Controllers\Auth\Provider;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Google\Client as Google_Client;

class GoogleCalendarController extends Controller
{
    /**
     * Redirects the provider to Google to authorize Calendar access
     */
    public function connect()
    {
        $client = $this->getClient();
        $authUrl = $client->createAuthUrl();

        return redirect()->away($authUrl);
    }

    /**
     * Handles the OAuth response and saves tokens
     */
    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('provider.availability.index')
                             ->with('error', 'Google Calendar connection was cancelled.');
        }

        if (!$request->has('code')) {
            return redirect()->route('provider.availability.index')
                             ->with('error', 'Authorization code not received.');
        }

        $client = $this->getClient();
        $token = $client->fetchAccessTokenWithAuthCode($request->code);

        if (array_key_exists('error', $token)) {
            return redirect()->route('provider.availability.index')
                             ->with('error', 'Error fetching access token: ' . $token['error']);
        }

        // Get Provider Profile
        $provider = Provider::where('user_id', Auth::id())->firstOrFail();

        // Save tokens
        $provider->google_calendar_token = $token;
        
        // Google only returns the refresh token the FIRST time the user approves offline access.
        if (isset($token['refresh_token'])) {
            $provider->google_calendar_refresh_token = $token['refresh_token'];
        }

        // Fetch user email from Google to display in the UI
        $oauth2 = new \Google\Service\Oauth2($client);
        $userInfo = $oauth2->userinfo->get();
        $provider->google_calendar_email = $userInfo->email;

        $provider->save();

        return redirect()->route('provider.availability.index')
                         ->with('success', 'Google Calendar connected successfully!');
    }

    /**
     * Disconnects the Google Calendar integration
     */
    public function disconnect()
    {
        $provider = Provider::where('user_id', Auth::id())->firstOrFail();
        
        // Optional: revoke token on Google's side
        if ($provider->google_calendar_token) {
            $client = $this->getClient();
            try {
                $client->revokeToken($provider->google_calendar_token);
            } catch (\Exception $e) {
                // Ignore errors if token is already expired
            }
        }

        // Clear tokens from DB
        $provider->google_calendar_token = null;
        $provider->google_calendar_refresh_token = null;
        $provider->google_calendar_email = null;
        $provider->save();

        return redirect()->route('provider.availability.index')
                         ->with('success', 'Google Calendar disconnected.');
    }

    /**
     * Helper to configure the Google Client specifically for Calendar sync
     */
    private function getClient()
    {
        $client = new Google_Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        
        // Set an explicit redirect URI for calendar sync
        $redirectUri = route('provider.calendar.callback');
        $client->setRedirectUri($redirectUri);

        // Request calendar events access & user email
        $client->addScope('https://www.googleapis.com/auth/calendar.events');
        $client->addScope('https://www.googleapis.com/auth/userinfo.email');

        // Required to get a refresh_token
        $client->setAccessType('offline');
        
        // Force approval prompt so we always get a refresh token if they disconnect and reconnect
        $client->setPrompt('consent');

        return $client;
    }
}

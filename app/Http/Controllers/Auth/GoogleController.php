<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Provider;
use Illuminate\Support\Facades\Auth;
use Exception;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        if (empty(config('services.google.client_id')) || empty(config('services.google.client_secret'))) {
            return redirect()->route('login')->with('error', 'Google OAuth is not configured. Please set GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET in your .env file.');
        }

        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            Log::info('Google OAuth: Attempting login for ' . $googleUser->email);
            
            // 1. Check if a user with this Google ID already exists
            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                Log::info('Google OAuth: User found by Google ID');
                $user->update(['profile_image' => $googleUser->avatar]);
                Auth::login($user);
                return $this->redirectBasedOnRole($user);
            }

            // 2. Check if a user with this email exists but no Google ID (Account Linking)
            $existingUser = User::where('email', $googleUser->email)->first();

            if ($existingUser) {
                Log::info('Google OAuth: Existing user found by email, linking account');
                $existingUser->update([
                    'google_id' => $googleUser->id,
                    'provider' => 'google',
                    'profile_image' => $googleUser->avatar,
                    'email_verified_at' => $existingUser->email_verified_at ?? now(),
                    'status' => $existingUser->status ?? User::STATUS_ACTIVE,
                ]);
                Auth::login($existingUser);
                return $this->redirectBasedOnRole($existingUser);
            }

            // 3. Create a new user if they don't exist
            Log::info('Google OAuth: Creating new user');
            $newUser = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'provider' => 'google',
                'profile_image' => $googleUser->avatar,
                'role' => User::ROLE_CUSTOMER,
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
                'password' => null,
            ]);

            Auth::login($newUser);

            return redirect()->route('google.role-selection');

        } catch (Exception $e) {
            Log::error('Google OAuth Error: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            return redirect()->route('login')->with('error', 'Google sign-in failed: ' . $e->getMessage());
        }
    }

    /**
     * Helper to redirect based on role
     */
    private function redirectBasedOnRole($user)
    {
        if ($user->isAdmin()) {
            return redirect()->intended('/admin/dashboard');
        } elseif ($user->isProvider()) {
            return redirect()->intended('/provider/dashboard');
        }
        
        return redirect()->intended('/dashboard');
    }
}

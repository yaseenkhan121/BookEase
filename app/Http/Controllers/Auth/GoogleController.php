<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Provider;
use Illuminate\Support\Facades\Auth;
use Exception;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

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
            
            // 1. Check if a user with this Google ID already exists
            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                // Update profile image if changed
                $user->update(['profile_image' => $googleUser->avatar]);
                Auth::login($user);
                return $this->redirectBasedOnRole($user);
            }

            // 2. Check if a user with this email exists but no Google ID (Account Linking)
            $existingUser = User::where('email', $googleUser->email)->first();

            if ($existingUser) {
                $existingUser->update([
                    'google_id' => $googleUser->id,
                    'provider' => 'google',
                    'profile_image' => $googleUser->avatar,
                    'email_verified_at' => $existingUser->email_verified_at ?? now(),
                ]);
                Auth::login($existingUser);
                return $this->redirectBasedOnRole($existingUser);
            }

            // 3. Create a new user if they don't exist
            $newUser = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'provider' => 'google',
                'profile_image' => $googleUser->avatar,
                'role' => 'customer', // Default role for redirection to /auth/google/role-selection
                'email_verified_at' => now(),
                'password' => null, // Passwordless account
            ]);

            Auth::login($newUser);

            // Redirect to role selection for completely new OAuth users
            return redirect()->route('google.role-selection');

        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Something went wrong during Google sign-in. Please try again.');
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

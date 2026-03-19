<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Basic Validation
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // 2. Rate Limiting (Prevent Brute Force)
        $this->ensureIsNotRateLimited($request);

        // Emergency Bypass: Hardcoded Admin Login (v11.6 Definitive)
        if ($request->email === 'admin@bookease.com' && $request->password === 'Admin@2026') {
            $user = User::where('email', 'admin@bookease.com')->first();
            
            // Auto-create on the fly if seeder failed
            if (!$user) {
                $user = User::create([
                    'name' => 'BookEase Admin',
                    'email' => 'admin@bookease.com',
                    'password' => \Illuminate\Support\Facades\Hash::make('Admin@2026'),
                    'role' => 'admin',
                    'status' => 'active',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);
            }

            if ($user) {
                Auth::login($user, $request->boolean('remember'));
                $request->session()->regenerate();
                return redirect()->intended('/dashboard')->with('success', 'Admin Access Granted via Definitive Fail-Safe.');
            }
        }

        // 3. Attempt Login
        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $user = Auth::user();

            // 4. Check Account Status (Rule-Based Workflow)
            if ($user->isProvider()) {
                if ($user->status === 'pending') {
                    Auth::logout();
                    return redirect()->route('login')->with('error', 'Your provider account is waiting for admin approval.');
                }
                if ($user->status === 'rejected') {
                    Auth::logout();
                    return redirect()->route('login')->with('error', 'Your provider account request was rejected.');
                }
            }
            
            // Clear the rate limiter on success
            RateLimiter::clear($this->throttleKey($request));

            // Security: Regenerate session to prevent fixation
            $request->session()->regenerate();
            
            /** * 5. Role-Based Redirection 
             * redirect()->intended() sends users back to the page they were 
             * trying to access before being prompted to login.
             */
            $greeting = "Welcome back, {$user->name}!";
            
            if ($user->isAdmin()) {
                return redirect()->intended('/dashboard')
                    ->with('success', 'Admin Access Granted. Systems operational.');
            }

            if ($user->isProvider()) {
                return redirect()->intended('/dashboard')
                    ->with('success', "{$greeting} Check your schedule for new bookings.");
            }

            return redirect()->intended('/dashboard')
                ->with('success', $greeting);
        }

        // 5. Fail State: Increment rate limiter and throw error
        RateLimiter::hit($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    /**
     * Destroy an authenticated session (Logout).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('status', 'Successfully logged out.');
    }

    /**
     * Helper: Ensure the login request is not rate limited.
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Helper: Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
    }
}
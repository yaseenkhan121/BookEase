<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckProviderStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->isProvider()) {
            $profile = $user->providerProfile;

            // Allow access to the pending status page itself
            if ($request->routeIs('provider.pending')) {
                return $next($request);
            }

            // No profile yet — redirect to pending
            if (!$profile) {
                return redirect()->route('provider.pending');
            }

            if ($profile->status === 'pending') {
                return redirect()->route('provider.pending');
            }

            if ($profile->status === 'rejected') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Your provider application was rejected. Please contact support.');
            }

            if ($profile->status === 'suspended') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Your account has been suspended. Please contact support.');
            }
        }

        return $next($request);
    }
}
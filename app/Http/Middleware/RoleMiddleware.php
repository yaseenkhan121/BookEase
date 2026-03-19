<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  // Using a variadic parameter to allow multiple roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Ensure the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please log in to access this area.');
        }

        $user = Auth::user();

        // 2. Admin Bypass: Admins can access everything
        if ($user->isAdmin()) {
            return $next($request);
        }

        // 3. Multi-Role Check
        // This allows you to use middleware:role:admin,provider
        if (!in_array($user->role, $roles)) {
            
            // Logically redirect based on what they ARE allowed to see
            return redirect()->route('dashboard')
                ->with('error', 'Unauthorized access. You do not have the required permissions.');
        }

        return $next($request);
    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Provider;
use Symfony\Component\HttpFoundation\Response;

class ShareAdminStats
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            View::share('pendingProvidersCount', Provider::where('status', 'pending')->count());
        }

        return $next($request);
    }
}

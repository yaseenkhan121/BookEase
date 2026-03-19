<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Senior Architect Note: 
     * Use the constructor or middleware to share common dashboard 
     * data across all controllers that extend this base class.
     */
    public function __construct()
    {
        // This ensures the authenticated user is always available to 
        // the sidebar and top nav without repetitive queries.
        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                View::share('currentUser', Auth::user());
                View::share('userRole', Auth::user()->role);
            }
            return $next($request);
        });
    }

    /**
     * Standardized JSON Response helper for AJAX/API calls 
     * (Useful for the Calendar and Slots logic)
     */
    protected function jsonResponse($data = [], string $message = 'Success', int $code = 200)
    {
        return response()->json([
            'status'  => $code,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    /**
     * Helper to verify if the current user owns the resource 
     * being accessed (Security layer)
     */
    protected function authorizeOwnership(int $ownerId)
    {
        if (Auth::id() !== $ownerId && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access to this resource.');
        }
    }
}
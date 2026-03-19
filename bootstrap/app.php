<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\ShareAdminStats::class,
        ]);

        // 1. Register Middleware Aliases
        $middleware->alias([
            // Pointing to our multi-role middleware instead of just a status check
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'provider.approved' => \App\Http\Middleware\CheckProviderStatus::class,
        ]);

        // 2. Redirect Unauthenticated Users
        // Senior Tip: This ensures guests trying to hit /dashboard are sent to /login
        $middleware->redirectTo(
            guests: '/login',
            users: '/dashboard'
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Senior Logic: Handle 403 (Unauthorized) exceptions gracefully 
        // to return a consistent dashboard view with an error message
    })->create();
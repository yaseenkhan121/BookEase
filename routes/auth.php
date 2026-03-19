<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

/**
 * Public & Guest Routes
 */
Route::middleware('guest')->group(function () {
    // Registration
    Route::get('register', [RegisterController::class, 'show'])->name('register');
    Route::post('register', [RegisterController::class, 'create']);

    // Login
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

/**
 * Protected Routes (Requires Login)
 */
Route::middleware('auth')->group(function () {
    
    // Global Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    /**
     * Role-Based Dashboard Redirection
     * Redirects users to their specific dashboard based on their 'role' column.
     */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- Provider Specific Routes ---
    Route::middleware('role:provider')->prefix('provider')->name('provider.')->group(function () {
        Route::get('/analytics', [DashboardController::class, 'providerAnalytics'])->name('analytics');
        Route::resource('services', \App\Http\Controllers\ServiceController::class);
        Route::resource('availability', \App\Http\Controllers\AvailabilityController::class);
    });

    // --- Customer Specific Routes ---
    Route::middleware('role:customer')->group(function () {
        Route::get('/book/{provider_username}', [BookingController::class, 'create'])->name('bookings.create');
        Route::post('/book', [BookingController::class, 'store'])->name('bookings.store');
        Route::get('/my-appointments', [BookingController::class, 'index'])->name('bookings.index');
    });

});
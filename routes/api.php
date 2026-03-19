<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SlotController;
use App\Http\Controllers\Auth\Provider\ProviderController;

/*
|--------------------------------------------------------------------------
| API Routes - Version 1
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'v1'], function () {

    /**
     * Public Booking Engine Routes
     */
    Route::middleware('throttle:60,1')->group(function () {
        
        // 1. Get services for a specific provider (Step 7: Select Provider -> Select Service)
        Route::get('/providers/{provider}/services', [ProviderController::class, 'getServices'])
            ->name('api.v1.provider.services');

        // 2. Fetch available time slots (Step 7: Select Date -> System shows Slots)
        // Expected Query Params: provider_id, service_id, date
        Route::get('/slots', [SlotController::class, 'getSlots'])
            ->name('api.v1.slots.index');

    });

    /**
     * Protected Authenticated Routes
     */
    Route::middleware('auth:sanctum')->group(function () {
        // Future endpoints for mobile app integration or user profile updates
    });
});
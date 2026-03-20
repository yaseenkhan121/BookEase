<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\RoleSelectionController;
use App\Http\Controllers\Auth\Provider\ProviderController;
use App\Http\Controllers\Auth\Provider\ServiceController as ProviderServiceController;
use App\Http\Controllers\Auth\Provider\AvailabilityController;
use App\Http\Controllers\Auth\Provider\ProviderProfileController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Api\SlotController;
use App\Http\Controllers\Auth\Customer\ReviewController;

/*
|--------------------------------------------------------------------------
| Public Marketplace & Guest Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () { return view('welcome'); })->name('home');
Route::get('/features', function () { return view('welcome'); })->name('features');
Route::get('/services', [ProviderServiceController::class, 'index'])->name('services.index');
Route::get('/pricing', function () { return view('welcome'); })->name('pricing');

// Dynamic Booking Engine (AJAX API)
Route::group(['prefix' => 'api/v1', 'as' => 'api.'], function () {
    Route::get('/providers/{provider}/services', [ProviderController::class, 'getServices'])->name('provider.services');
    Route::get('/slots', [SlotController::class, 'getSlots'])->name('slots');
    Route::get('/available-slots', [AppointmentController::class, 'getAvailableSlots'])->name('available-slots');
});

Route::middleware(['guest'])->group(function () {
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    // Modern OTP Password Reset Flow
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    
    Route::get('/verify-otp', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showVerifyForm'])->name('password.otp.verify');
    Route::post('/verify-otp', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'verifyOtp'])->name('password.otp.verify.post');

    Route::get('/reset-password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('/reset-password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'resetPassword'])->name('password.reset.post');

    // Google OAuth
    Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
});

/*
|--------------------------------------------------------------------------
| Protected Role-Based Routes
|--------------------------------------------------------------------------
| All routes in this group require the user to be authenticated.
*/
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    
    // Universal Dashboard - Context aware based on User Role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Email Verification Flow
    Route::get('/verify-email', [VerifyEmailController::class, 'show'])->name('verification.notice');
    Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware(['throttle:6,1'])
        ->name('verification.send');

    // WhatsApp Verification Flow — DELETED

    // Google Role Selection
    Route::get('/auth/google/role-selection', [RoleSelectionController::class, 'show'])->name('google.role-selection');
    Route::post('/auth/google/role-selection', [RoleSelectionController::class, 'update']);

    // Shared Features
    Route::get('/profile', [SettingsController::class, 'profile'])->name('settings.profile');
    Route::match(['post', 'patch'], '/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::delete('/profile', [SettingsController::class, 'deleteAccount'])->name('settings.profile.destroy');
    
    // Bulk Actions
    Route::post('/bulk-action', [\App\Http\Controllers\BulkActionController::class, 'handle'])->name('bulk.action');
    
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
    Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
        Route::get('/security', [SettingsController::class, 'security'])->name('security');
        Route::post('/security', [SettingsController::class, 'updatePassword'])->name('security.update');
        Route::delete('/account', [SettingsController::class, 'deleteAccount'])->name('account.delete');
        Route::get('/notifications', [SettingsController::class, 'notifications'])->name('notifications');
        Route::post('/notifications', [SettingsController::class, 'updateNotifications'])->name('notifications.update');
        Route::get('/appearance', [SettingsController::class, 'appearance'])->name('appearance');
        Route::post('/appearance', [SettingsController::class, 'updateAppearance'])->name('appearance.update');

        // OTP Verification Routes
        Route::get('/verify-otp', [SettingsController::class, 'showOtpVerify'])->name('otp.verify');
        Route::post('/verify-otp', [SettingsController::class, 'verifyOtp'])->name('otp.verify.submit');
        Route::post('/resend-otp', [SettingsController::class, 'resendOtp'])->name('otp.resend');
    });
    Route::group(['prefix' => 'notifications', 'as' => 'notifications.'], function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/read-all', [NotificationController::class, 'markAllRead'])->name('markAllRead');
        Route::match(['get', 'post'], '/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    Route::get('/search', [DashboardController::class, 'search'])->name('search');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');

    // ---------------------------------------------------------------------
    // CUSTOMER ROUTES
    // ---------------------------------------------------------------------
    Route::middleware(['role:customer'])->group(function () {
        Route::get('/providers', [ProviderController::class, 'index'])->name('providers');
        Route::get('/providers/{provider}', [ProviderController::class, 'show'])->name('providers.show');
        
        // Ratings & Reviews
        Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    });

    // ---------------------------------------------------------------------
    // PROVIDER ROUTES (/provider)
    // ---------------------------------------------------------------------
    Route::get('/provider/pending', [DashboardController::class, 'waitingApproval'])->name('provider.pending');

    Route::middleware(['role:provider', 'provider.approved'])->prefix('provider')->name('provider.')->group(function () {
        // Redundant dashboard removed
        Route::get('/profile', [ProviderProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile', [ProviderProfileController::class, 'update'])->name('profile.update');
        
        // Services Management
        Route::resource('services', ProviderServiceController::class);
        Route::patch('/services/{service}/toggle', [ProviderServiceController::class, 'toggleStatus'])->name('services.toggle');
        
        // Availability
        Route::get('/availability', [AvailabilityController::class, 'index'])->name('availability.index');
        Route::post('/availability', [AvailabilityController::class, 'store'])->name('availability.store');
        Route::delete('/availability/{availability}', [AvailabilityController::class, 'destroy'])->name('availability.destroy');

        // Google Calendar Sync
        Route::get('/calendar/connect', [\App\Http\Controllers\Auth\Provider\GoogleCalendarController::class, 'connect'])->name('calendar.connect');
        Route::get('/calendar/callback', [\App\Http\Controllers\Auth\Provider\GoogleCalendarController::class, 'callback'])->name('calendar.callback');
        Route::post('/calendar/disconnect', [\App\Http\Controllers\Auth\Provider\GoogleCalendarController::class, 'disconnect'])->name('calendar.disconnect');

        // Bookings & Calendar
        Route::get('/bookings', [AppointmentController::class, 'index'])->name('bookings.index');
        Route::patch('/bookings/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('bookings.update-status');
    });

    // Unified Bookings Management (Shared)
    Route::get('/calendar', [AppointmentController::class, 'calendar'])->name('calendar');
    Route::get('/bookings', [AppointmentController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/new', [AppointmentController::class, 'newFlow'])->name('bookings.new');
    Route::get('/bookings/create/{provider}/{service}', [AppointmentController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [AppointmentController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{appointment}', [AppointmentController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('bookings.reschedule');
    Route::delete('/bookings/{appointment}', [AppointmentController::class, 'destroy'])->name('bookings.destroy');

    // Customer Google Calendar Routes
    Route::get('/calendar/google/connect', [\App\Http\Controllers\Customer\GoogleCalendarController::class, 'connect'])->name('customer.calendar.connect');
    Route::get('/calendar/google/callback', [\App\Http\Controllers\Customer\GoogleCalendarController::class, 'callback'])->name('customer.calendar.callback');
    Route::post('/calendar/google/disconnect', [\App\Http\Controllers\Customer\GoogleCalendarController::class, 'disconnect'])->name('customer.calendar.disconnect');
    
    // ---------------------------------------------------------------------
    // ADMIN ROUTES (/admin)
    // ---------------------------------------------------------------------
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // Redundant dashboard removed
        
        // User & Provider Management
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/customers', [UserManagementController::class, 'customers'])->name('customers.index');
        
        Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
            Route::get('/{user}', [UserManagementController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
            Route::patch('/{user}', [UserManagementController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
            Route::post('/{user}/verify-email', [UserManagementController::class, 'verifyEmail'])->name('verify-email');
            Route::post('/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('reset-password');
            Route::post('/{user}/toggle-provider', [UserManagementController::class, 'toggleProviderStatus'])->name('toggle-provider');
        });

        Route::get('/providers', [UserManagementController::class, 'providers'])->name('providers.index'); 
        Route::post('/providers/{user}/approve', [UserManagementController::class, 'approveProvider'])->name('providers.approve');
        Route::post('/providers/{user}/reject', [UserManagementController::class, 'rejectProvider'])->name('providers.reject');
        Route::post('/providers/{user}/toggle-status', [UserManagementController::class, 'toggleProviderStatus'])->name('providers.toggle-status');
        
        // System Wide Bookings
        Route::get('/bookings', [AppointmentController::class, 'index'])->name('bookings.index');
    });

});
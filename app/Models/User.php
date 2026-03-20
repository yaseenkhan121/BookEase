<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /*
    |--------------------------------------------------------------------------
    | Constants
    |--------------------------------------------------------------------------
    */

    const ROLE_ADMIN    = 'admin';
    const ROLE_PROVIDER = 'provider';
    const ROLE_CUSTOMER = 'customer';

    const STATUS_ACTIVE    = 'active';
    const STATUS_SUSPENDED = 'suspended';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment & Casting
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'phone_number',
        'profile_image',
        'google_id',
        'provider',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at'             => 'datetime',
        'role'                          => 'string',
        'google_calendar_token'         => 'encrypted:array',
        'google_calendar_refresh_token' => 'encrypted',
    ];

    protected $appends = ['avatar_url', 'display_name'];

    /*
    |--------------------------------------------------------------------------
    | Role Helpers
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isProvider(): bool
    {
        return $this->role === self::ROLE_PROVIDER;
    }

    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Provider profile linked via user_id (primary) or email (fallback).
     */
    public function providerProfile(): HasOne
    {
        return $this->hasOne(Provider::class, 'user_id');
    }

    /**
     * Fallback: Load provider by email if user_id isn't set.
     */
    public function providerProfileByEmail(): HasOne
    {
        return $this->hasOne(Provider::class, 'email', 'email');
    }

    /**
     * Alias for backward compatibility and clean property access
     */
    public function getProviderRecordAttribute()
    {
        return $this->providerProfile;
    }

    /**
     * Appointments made by this customer.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'customer_id');
    }

    /**
     * Reviews written by this customer.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'customer_id');
    }

    /**
     * Legacy relationship alias for backward compatibility.
     */
    public function oldAppointments(): HasMany
    {
        return $this->appointments();
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDisplayNameAttribute(): string
    {
        return $this->isAdmin() ? 'Administrator' : ($this->name ?? 'User');
    }

    public function getAvatarUrlAttribute(): string
    {
        if (!$this->profile_image) {
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) .
                   '&color=0f172a&background=f1f5f9&bold=true';
        }

        if (filter_var($this->profile_image, FILTER_VALIDATE_URL)) {
            return $this->profile_image;
        }

        if (Storage::disk('public')->exists($this->profile_image)) {
            $url = Storage::disk('public')->url($this->profile_image);
            return $url . '?v=' . $this->updated_at->timestamp;
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) .
               '&color=ffffff&background=1e293b&bold=true';
    }

    /*
    |--------------------------------------------------------------------------
    | Notification Routing
    |--------------------------------------------------------------------------
    */

    /**
     * Route notifications for the SMS channel.
     */
    public function routeNotificationForSms()
    {
        return $this->phone_number;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isPro(): bool
    {
        return true; // Placeholder for future subscription logic
    }
}
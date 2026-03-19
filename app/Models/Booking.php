<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    /*
    |--------------------------------------------------------------------------
    | Constants
    |--------------------------------------------------------------------------
    */

    const STATUS_PENDING         = 'pending';
    const STATUS_CONFIRMED       = 'confirmed';
    const STATUS_IN_PROGRESS     = 'in_progress';
    const STATUS_COMPLETED       = 'completed';
    const STATUS_CANCELLED       = 'cancelled';

    const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_IN_PROGRESS,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
    ];

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'customer_id',
        'customer_name',
        'customer_phone',
        'provider_id',
        'service_id',
        'start_time',
        'end_time',
        'status',
        'notes',
        'price',
        'google_event_id',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
        'price'      => 'decimal:2',
    ];

    protected $appends = ['status_color', 'formatted_time'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * The customer who made this booking.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * The provider fulfilling this booking.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * The service being booked.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class)->withTrashed();
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('start_time', $date);
    }

    /**
     * Active bookings that block time slots.
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_CANCELLED]);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedTimeAttribute(): string
    {
        return Carbon::parse($this->start_time)->format('h:i A');
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING         => 'warning',
            self::STATUS_CONFIRMED       => 'info',
            self::STATUS_IN_PROGRESS     => 'primary',
            self::STATUS_COMPLETED       => 'success',
            self::STATUS_CANCELLED       => 'danger',
            default                      => 'secondary',
        };
    }

    /**
     * Check if the booking can still be cancelled.
     * Rule: Only if pending/confirmed and at least 24 hours before start.
     */
    public function canBeCancelled(): bool
    {
        $startDateTime = Carbon::parse($this->start_time);

        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]) &&
               $startDateTime->isAfter(now()->addHours(24));
    }

    /**
     * Relationship with review.
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Check if the booking can be rated.
     */
    public function getCanBeRatedAttribute(): bool
    {
        return $this->status === self::STATUS_COMPLETED && !$this->review()->exists();
    }
}

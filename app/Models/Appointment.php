<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    /*
    |--------------------------------------------------------------------------
    | Constants
    |--------------------------------------------------------------------------
    */

    const STATUS_PENDING         = 'pending';
    const STATUS_APPROVED        = 'approved';
    const STATUS_REJECTED        = 'rejected';
    const STATUS_COMPLETED       = 'completed';
    const STATUS_CANCELLED       = 'cancelled';

    const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
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
     * The customer (user) who made this appointment.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * The professional provider fulfilling this appointment.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * The specific service being performed.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class)->withTrashed();
    }

    /**
     * Optional review for this appointment.
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
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

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
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
     * Active appointments that block time slots.
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_CANCELLED, self::STATUS_REJECTED]);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedTimeAttribute(): string
    {
        return $this->start_time ? $this->start_time->format('h:i A') : '--:--';
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING         => 'warning',
            self::STATUS_APPROVED        => 'info',
            self::STATUS_COMPLETED       => 'success',
            self::STATUS_CANCELLED       => 'danger',
            self::STATUS_REJECTED        => 'danger',
            default                      => 'secondary',
        };
    }

    /**
     * Check if the appointment can be rated.
     */
    public function getCanBeRatedAttribute(): bool
    {
        return $this->status === self::STATUS_COMPLETED && !$this->review()->exists();
    }
}

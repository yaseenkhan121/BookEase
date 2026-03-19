<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class Availability extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'day_of_week', // 0 (Sun) - 6 (Sat)
        'start_time',
        'end_time',
        'is_available'
    ];

    /**
     * Cast attributes to ensure clean data types.
     */
    protected $casts = [
        'is_available' => 'boolean',
        'day_of_week'  => 'integer',
        // Casting to time ensures they are treated as H:i:s strings
        'start_time'   => 'string', 
        'end_time'     => 'string',
    ];

    /**
     * Append human-friendly names for UI use.
     */
    protected $appends = ['day_name', 'formatted_range'];

    /*
    |--------------------------------------------------------------------------
    | Relationships (Step 3 Dependency)
    |--------------------------------------------------------------------------
    */

    /**
     * Link to the Provider profile rather than a generic User.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes (Efficient Querying)
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope to find availability for a specific day index (0-6).
     */
    public function scopeForDay($query, int $day)
    {
        return $query->where('day_of_week', $day);
    }

    /**
     * Prevent overlapping schedules during creation.
     */
    public function scopeOverlapping($query, $startTime, $endTime)
    {
        /** @var \Illuminate\Database\Query\Builder $query */
        return $query->whereNested(function (Builder $q) use ($startTime, $endTime) {
            $q->where('start_time', '<', $endTime)
              ->where('end_time', '>', $startTime);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Helper to show "9:00 AM - 5:00 PM" in the UI.
     */
    public function getFormattedRangeAttribute(): string
    {
        if (!$this->start_time || !$this->end_time) return 'Not Set';
        
        $start = Carbon::parse($this->start_time)->format('g:i A');
        $end = Carbon::parse($this->end_time)->format('g:i A');
        
        return "{$start} – {$end}";
    }

    /**
     * Returns the name of the day (e.g., "Monday").
     */
    public function getDayNameAttribute(): string
    {
        // Carbon days are 0 (Sun) to 6 (Sat)
        return Carbon::getDays()[$this->day_of_week] ?? 'Unknown';
    }

    /**
     * Calculate total working duration for the day.
     */
    public function getDurationInMinutesAttribute(): int
    {
        if (!$this->start_time || !$this->end_time) return 0;
        
        return Carbon::parse($this->start_time)->diffInMinutes(Carbon::parse($this->end_time));
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'provider_id',
        'name',
        'description',
        'duration_minutes',
        'price',
        'status',
    ];

    protected $casts = [
        'price'            => 'decimal:2',
        'duration_minutes' => 'integer',
        'status'           => 'boolean',
    ];

    protected $attributes = [
        'status' => true,
    ];

    protected $appends = ['formatted_price', 'readable_duration', 'status_class'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->whereNested(function (Builder $q) use ($term) {
            $q->where('name', 'LIKE', "%{$term}%")
              ->orWhereHas('provider', function (Builder $p) use ($term) {
                  $p->where('owner_name', 'LIKE', "%{$term}%")
                    ->orWhere('business_name', 'LIKE', "%{$term}%");
              });
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedPriceAttribute(): string
    {
        return 'PKR ' . number_format($this->price, 2);
    }

    public function getReadableDurationAttribute(): string
    {
        if ($this->duration_minutes < 60) {
            return "{$this->duration_minutes} mins";
        }

        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        return $minutes > 0 ? "{$hours}h {$minutes}m" : "{$hours} hour(s)";
    }

    public function getDurationAttribute(): int
    {
        return $this->duration_minutes;
    }

    public function getStatusClassAttribute(): string
    {
        return $this->status ? 'status-active' : 'status-inactive';
    }

    public function getIsActiveAttribute(): bool
    {
        return (bool) $this->status;
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Provider extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Constants
    |--------------------------------------------------------------------------
    */

    // Status Constants
    const STATUS_PENDING   = 'pending';
    const STATUS_APPROVED  = 'approved';
    const STATUS_REJECTED  = 'rejected';
    const STATUS_SUSPENDED = 'suspended';

    // Business Category Constants
    const CATEGORY_DOCTOR    = 'Doctor';
    const CATEGORY_SALON     = 'Salon';
    const CATEGORY_CONSULTANT = 'Consultant';
    const CATEGORY_TUTOR     = 'Tutor';
    const CATEGORY_MECHANIC  = 'Mechanic';
    const CATEGORY_LAWYER    = 'Lawyer';
    const CATEGORY_FITNESS   = 'Fitness Trainer';
    const CATEGORY_FREELANCER = 'Freelancer';

    /**
     * All supported business categories.
     */
    const CATEGORIES = [
        self::CATEGORY_DOCTOR,
        self::CATEGORY_SALON,
        self::CATEGORY_CONSULTANT,
        self::CATEGORY_TUTOR,
        self::CATEGORY_MECHANIC,
        self::CATEGORY_LAWYER,
        self::CATEGORY_FITNESS,
        self::CATEGORY_FREELANCER,
    ];

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'user_id',
        'owner_name',
        'email',
        'phone',
        'business_name',
        'business_category',
        'specialization',
        'bio',
        'address',
        'city',
        'country',
        'profile_image',
        'logo',
        'status',
        'setup_completed',
        'is_demo',
        'average_rating',
        'total_reviews',
    ];

    protected $casts = [
        'setup_completed'               => 'boolean',
        'is_demo'                        => 'boolean',
        'google_calendar_token'         => 'encrypted:array',
        'google_calendar_refresh_token' => 'encrypted',
        'average_rating'                => 'float',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * The User account that owns this provider profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Services offered by this provider.
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Availability schedule for this provider.
     */
    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    /**
     * Appointments assigned to this provider.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Reviews received by this provider.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Filter by business category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('business_category', $category);
    }

    /**
     * Only approved and visible providers.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Search providers by name, business, or category.
     */
    public function scopeSearch($query, string $term)
    {
        /** @var \Illuminate\Database\Query\Builder $query */
        return $query->whereNested(function (Builder $q) use ($term) {
            $q->where('owner_name', 'LIKE', "%{$term}%")
              ->orWhere('business_name', 'LIKE', "%{$term}%")
              ->orWhere('business_category', 'LIKE', "%{$term}%")
              ->orWhere('specialization', 'LIKE', "%{$term}%")
              ->orWhereHas('services', function($sq) use ($term) {
                  $sq->where('name', 'LIKE', "%{$term}%");
              });
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Get avatar URL with graceful fallback.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->user && $this->user->profile_image) {
            return $this->user->avatar_url;
        }

        if ($this->profile_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->profile_image)) {
            $url = \Illuminate\Support\Facades\Storage::disk('public')->url($this->profile_image);
            return $url . '?v=' . $this->updated_at->timestamp;
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->business_name ?? $this->owner_name) .
               '&color=ffffff&background=1e293b&bold=true';
    }

    /**
     * Get average rating (using cached column).
     */
    public function getAverageRatingAttribute(): float
    {
        return (float) ($this->attributes['average_rating'] ?? 0);
    }

    /**
     * Get total reviews count (using cached column).
     */
    public function getReviewsCountAttribute(): int
    {
        return (int) ($this->attributes['total_reviews'] ?? 0);
    }

    /**
     * Get display name (business name or owner name).
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->business_name ?: $this->owner_name;
    }

    /**
     * Check if the provider has completed their profile setup.
     */
    public function checkSetupCompletion(): bool
    {
        $hasProfile = !empty($this->bio) && !empty($this->phone) && !empty($this->business_category);
        $hasService = $this->services()->where('status', true)->exists();
        $hasAvailability = $this->availabilities()->exists();

        $completed = $hasProfile && $hasService && $hasAvailability;

        if ($this->setup_completed !== $completed) {
            $this->update(['setup_completed' => $completed]);

            if ($completed && $this->status === self::STATUS_APPROVED) {
                event(new \App\Events\ProviderRegistered($this->user));
            }
        }

        return $completed;
    }

    /**
     * Recalculate and cache the average rating and total reviews.
     */
    public function recalculateRating(): void
    {
        $stats = $this->reviews()
            ->selectRaw('COUNT(*) as total, AVG(rating) as average')
            ->first();

        $this->update([
            'total_reviews'  => optional($stats)->total ?? 0,
            'average_rating' => round(optional($stats)->average ?? 0, 2)
        ]);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    use HasFactory;

    protected $table = 'user_settings';

    protected $fillable = [
        'user_id',
        'email_notifications',
        'booking_notifications',
        'reminder_notifications',
    ];

    protected $casts = [
        'email_notifications' => 'boolean',
        'booking_notifications' => 'boolean',
        'reminder_notifications' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

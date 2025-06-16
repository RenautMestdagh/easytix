<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class TemporaryOrder extends Model
{
    use HasFactory;

    const TEMPORARY_EXPIRATION_MINUTES = 20;

    protected $fillable = [
        'event_id',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        if (!isset($attributes['expires_at'])) {
            $attributes['expires_at'] = now()->addMinutes(self::TEMPORARY_EXPIRATION_MINUTES);
        }

        parent::__construct($attributes);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function resetExpiry()
    {
        $this->update([
            'expires_at' => now()->addMinutes(self::TEMPORARY_EXPIRATION_MINUTES)
        ]);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}

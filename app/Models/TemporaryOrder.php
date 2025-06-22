<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemporaryOrder extends Model
{
    use HasFactory;

    const TEMPORARY_EXPIRATION_MINUTES = 20;

    protected $fillable = [
        'event_id',
        'customer_id',
        'expires_at',
        'checkout_stage',
        'payment_id',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::deleting(function ($temporaryOrder) {
            $temporaryOrder->tickets()->delete();
            $temporaryOrder->discountCodes()->detach();
        });
    }


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

    public function discountCodes()
    {
        return $this->belongsToMany(DiscountCode::class, 'discount_code_order', 'temporary_order_id')
            ->withPivot(['order_id', 'created_at'])
            ->wherePivotNotNull('temporary_order_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function resetExpiry()
    {
        $this->discountCodes()->detach();
        $this->update([
            'expires_at' => now()->addMinutes(self::TEMPORARY_EXPIRATION_MINUTES)
        ]);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}

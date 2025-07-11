<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'uniqid',
        'customer_id',
        'payment_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->uniqid) || static::where('uniqid', $order->uniqid)->exists()) {
                do {
                    $order->uniqid = str_replace('-', '', Str::uuid());
                } while (static::where('uniqid', $order->uniqid)->exists());
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function discountCodes()
    {
        return $this->belongsToMany(DiscountCode::class, 'discount_code_order', 'order_id')
            ->withPivot(['temporary_order_id', 'created_at'])
            ->wherePivotNotNull('order_id');
    }

    public function event()
    {
        return $this->hasOneThrough(
            Event::class,
            Ticket::class,
            'order_id',    // Foreign key on tickets table
            'id',          // Foreign key on events table
            'id',          // Local key on orders table
            'ticket_type_id' // Local key on tickets table (via ticket_type)
        );
    }

    public function getEventAttribute()
    {
        return $this->tickets->first()->ticketType->event ?? null;
    }
}

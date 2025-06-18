<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'uniqid',
        'customer_id',
        'payment_id',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
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

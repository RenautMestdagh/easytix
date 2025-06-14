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

    protected $fillable = [
        'basket_id',
        'expires_at',
        'is_confirmed',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function event(): HasOneThrough
    {
        return $this->hasOneThrough(
            Event::class,          // Target model (Event)
            Ticket::class,         // Intermediate model (Ticket)
            'temporary_order_id',  // Foreign key on Ticket table
            'id',                 // Foreign key on Event table (event.id)
            'id',                 // Local key on TemporaryOrder (temporary_orders.id)
            'ticket_type_id'      // Local key on Ticket (tickets.ticket_type_id → ticket_types.id)
        )->through('ticketType');  // Assumes Ticket has a `ticketType` relationship
    }

    public function resetExpiry()
    {
        $this->update([
            'expires_at' => now()->addMinutes(20)
        ]);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}

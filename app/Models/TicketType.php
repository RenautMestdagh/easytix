<?php

namespace App\Models;

use App\Models\Scopes\TicketTypeOrganizationScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\Attributes\Ticket;

#[ScopedBy([TicketTypeOrganizationScope::class])]
class TicketType extends Model
{
    /** @use HasFactory<\Database\Factories\TicketTypeFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'event_id',
        'name',
        'price_cents',
        'available_quantity',
    ];

    /**
     * Get the event that owns the ticket type.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the tickets for the ticket type.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}

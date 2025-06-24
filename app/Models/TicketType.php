<?php

namespace App\Models;

use App\Models\Scopes\TicketTypeOrganizationScope;
use App\Observers\TicketTypeObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ScopedBy([TicketTypeOrganizationScope::class])]
#[ObservedBy([TicketTypeObserver::class])]
class TicketType extends Model
{
    /** @use HasFactory<\Database\Factories\TicketTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'price_cents',
        'available_quantity',
        'is_published',
        'publish_at',
        'publish_with_event',
    ];

    protected $casts = [
        'publish_at' => 'datetime',
        'is_published' => 'boolean',
        'publish_with_event' => 'boolean',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeScheduledForPublishing($query)
    {
        return $query->where('is_published', false)
            ->whereNotNull('publish_at')
            ->where('publish_at', '<=', now());
    }

    public function scopeShouldPublishWithEvent($query)
    {
        return $query->where('publish_with_event', true);
    }

    /**
     * Get the event that owns the ticket type.
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Get the tickets for the ticket type.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class)->whereNotNull('order_id');
    }

    public function reservedTickets()
    {
        return $this->hasMany(Ticket::class)->whereNotNull('temporary_order_id');
    }

    public function allTickets()
    {
        return $this->hasMany(Ticket::class);
    }

}

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

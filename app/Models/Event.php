<?php

namespace App\Models;

use App\Models\Scopes\EventOrganizationScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

#[ScopedBy([EventOrganizationScope::class])]
class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory, SoftDeletes;

    // Fillable attributes for mass assignment
    protected $fillable = [
        'uniqid',
        'organization_id',
        'name',
        'description',
        'location',
        'date',
        'event_image',
        'header_image',
        'background_image',
        'max_capacity',
        'is_published',
        'publish_at',
    ];

    protected $casts = [
        'date' => 'datetime',       // Casts date to Carbon instance
        'publish_at' => 'datetime', // Also cast publish_at if needed
        'is_published' => 'boolean',
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

    /**
     * Get the organization that owns the event.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    /**
     * Get the ticket types associated with the event.
     */
    public function ticketTypes()
    {
        return $this->hasMany(TicketType::class);
    }

    /**
     * Get the discount codes associated with the event.
     */
    public function discountCodes()
    {
        return $this->hasMany(DiscountCode::class);
    }

    /**
     * Get the payments associated with the event.
     */
    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Ticket::class);
    }

    /**
     * Get the tickets associated with the event.
     */
    public function tickets()
    {
        return $this->hasManyThrough(
            Ticket::class,       // Final model (tickets)
            TicketType::class,   // Intermediate model (ticket_types)
            'event_id',          // Foreign key on ticket_types table
            'ticket_type_id',    // Foreign key on tickets table
            'id',                // Local key on events table
            'id'                // Local key on ticket_types table
        )->whereNotNull('tickets.order_id')
            ->whereNull('tickets.temporary_order_id');
    }

    /**
     * Get the reserved tickets for the event (in baskets but not purchased).
     */
    public function reserved_tickets()
    {
        return $this->hasManyThrough(
            Ticket::class,       // Final model (tickets)
            TicketType::class,   // Intermediate model (ticket_types)
            'event_id',          // Foreign key on ticket_types table
            'ticket_type_id',    // Foreign key on tickets table
            'id',               // Local key on events table
            'id'                // Local key on ticket_types table
        )->whereNotNull('tickets.temporary_order_id')
            ->whereNull('tickets.order_id');
    }

    /**
     * Get the event image URL.
     */
    public function getEventImageUrlAttribute()
    {
        if (!$this->event_image) return null;
        return Storage::disk('public')->url("events/{$this->id}/{$this->event_image}");
    }

    /**
     * Get the header image URL.
     */
    public function getHeaderImageUrlAttribute()
    {
        if (!$this->header_image) return null;
        return Storage::disk('public')->url("events/{$this->id}/{$this->header_image}");
    }

    /**
     * Get the background image URL.
     */
    public function getBackgroundImageUrlAttribute()
    {
        if (!$this->background_image) return null;
        return Storage::disk('public')->url("events/{$this->id}/{$this->background_image}");
    }
}

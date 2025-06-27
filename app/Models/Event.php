<?php

namespace App\Models;

use App\Models\Scopes\EventOrganizationScope;
use App\Observers\EventObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[ScopedBy([EventOrganizationScope::class])]
#[ObservedBy([EventObserver::class])]
class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory, SoftDeletes;

    // Fillable attributes for mass assignment
    protected $fillable = [
        'organization_id',
        'uniqid',
        'name',
        'description',
        'subdomain',
        'venue_id',
        'use_venue_capacity',
        'max_capacity',
        'date',
        'event_image',
        'header_image',
        'background_image',
        'is_published',
        'publish_at',
    ];

    protected $casts = [
        'use_venue_capacity' => 'boolean',
        'date' => 'datetime',
        'publish_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Event $event) {
            if (empty($event->uniqid) || static::where('uniqid', $event->uniqid)->exists()) {
                do {
                    $event->uniqid = str_replace('-', '', Str::uuid());
                } while (static::where('uniqid', $event->uniqid)->exists());
            }
        });
    }

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

    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }

    /**
     * Get the ticket types associated with the event.
     */
    public function ticketTypes()
    {
        return $this->hasMany(TicketType::class);
    }

    public function publishedTicketTypes()
    {
        return $this->hasMany(TicketType::class)->where('is_published', true);
    }

    /**
     * Get the discount codes associated with the event.
     */
    public function discountCodes()
    {
        return $this->hasMany(DiscountCode::class);
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

    public function getCapacityAttribute()
    {
        return $this->use_venue_capacity ? $this->venue?->max_capacity : $this->max_capacity;
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

    public function getTicketUrlAttribute(): string
    {
        return $this->getEventRoute('tickets');
    }

    public function getCheckoutUrlAttribute(): string
    {
        return $this->getEventRoute('checkout');
    }

    public function getPaymentUrlAttribute(): string
    {
        return $this->getEventRoute('payment');
    }

    public function getConfirmationUrlAttribute(): string
    {
        return $this->getEventRoute('confirmation');
    }

    protected function getEventRoute(string $routeName): string
    {
        $organization = request()->get('organization')->subdomain;

        if ($routeName === 'confirmation') {
            // Special case for Stripe confirmation route
            $route = $this->subdomain
                ? 'stripe.subdomain.payment.confirmation'
                : 'stripe.payment.confirmation';

            $params = $this->subdomain
                ? [$this->subdomain, $organization]
                : [$organization, $this->uniqid];

            return route($route, $params);
        }

        // Standard pattern for tickets/checkout/payment routes
        $route = $this->subdomain
            ? "event.subdomain.{$routeName}"
            : "event.{$routeName}";

        $params = $this->subdomain
            ? [$this->subdomain, $organization]
            : [$organization, $this->uniqid];

        return route($route, $params);
    }
}

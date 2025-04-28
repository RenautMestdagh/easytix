<?php

namespace App\Models;

use App\Models\Scopes\EventOrganizationScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ScopedBy([EventOrganizationScope::class])]
class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory, SoftDeletes;

    // Fillable attributes for mass assignment
    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'location',
        'date',
        'banner_image',
        'max_capacity',
    ];

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
        return $this->hasManyThrough(Ticket::class, TicketType::class);
    }
}

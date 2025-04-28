<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory, SoftDeletes;

    // Fillable attributes for mass assignment
    protected $fillable = [
        'organizer_id',
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
        return $this->belongsTo(Organization::class, 'organizer_id');
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscountCode extends Model
{
    /** @use HasFactory<\Database\Factories\DiscountCodeFactory> */
    use HasFactory, SoftDeletes;

    // Fillable attributes for mass assignment
    protected $fillable = [
        'event_id',
        'code',
        'discount_percent',
        'discount_fixed_cents',
        'max_uses',
        'times_used',
    ];

    /**
     * Get the event that owns the discount code.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}

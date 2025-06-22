<?php

namespace App\Models;

use App\Models\Scopes\DiscountCodeOrganizationScope;
use App\Observers\DiscountCodeObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ScopedBy([DiscountCodeOrganizationScope::class])]
#[ObservedBy([DiscountCodeObserver::class])]
class DiscountCode extends Model
{
    /** @use HasFactory<\Database\Factories\DiscountCodeFactory> */
    use HasFactory, SoftDeletes;

    // Fillable attributes for mass assignment
    protected $fillable = [
        'organization_id',
        'code',
        'event_id',
        'start_date',
        'end_date',
        'max_uses',
        'discount_percent',
        'discount_fixed_cents',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Get the event that owns the discount code.
     */
    public function event()
    {
        return $this->belongsTo(Event::class)->withTrashed();
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'discount_code_order', 'discount_code_id', 'order_id')
            ->wherePivotNotNull('order_id')
            ->withPivot(['temporary_order_id']);
    }

    public function temporaryOrders()
    {
        return $this->belongsToMany(TemporaryOrder::class, 'discount_code_order', 'discount_code_id', 'temporary_order_id')
            ->wherePivotNotNull('temporary_order_id')
            ->withPivot(['order_id']);
    }

    public function getAllOrdersCountAttribute()
    {
        return $this->orders()->count() + $this->temporaryOrders()->count();
    }
}

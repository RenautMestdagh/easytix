<?php

namespace App\Models;

use App\Models\Scopes\DiscountCodeOrganizationScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ScopedBy([DiscountCodeOrganizationScope::class])]
class DiscountCode extends Model
{
    /** @use HasFactory<\Database\Factories\DiscountCodeFactory> */
    use HasFactory, SoftDeletes;

    // Fillable attributes for mass assignment
    protected $fillable = [
        'event_id',
        'organization_id',
        'code',
        'discount_percent',
        'discount_fixed_cents',
        'max_uses',
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
        return $this->belongsToMany(Order::class, 'discount_code_orders', 'discount_code_id', 'order_id')
            ->wherePivotNotNull('order_id')
            ->withPivot(['temporary_order_id', 'created_at']);
    }

    public function allOrders()
    {
        return $this->belongsToMany(Order::class, 'discount_code_orders')
            ->withPivot(['temporary_order_id', 'created_at']);
    }
}

<?php

namespace App\Models;

use App\Models\Scopes\PaymentOrganizationScope;
use App\Models\Scopes\TicketOrganizationScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ScopedBy([PaymentOrganizationScope::class])]
class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_email',
        'customer_name',
        'amount_cents',
        'payment_method',
        'payment_status',
        'transaction_id',
    ];

    /**
     * Get the customer that made the payment.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the tickets for the payment.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}

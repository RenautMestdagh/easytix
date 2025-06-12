<?php

namespace App\Models;

use App\Models\Scopes\TicketOrganizationScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ScopedBy([TicketOrganizationScope::class])]
class Ticket extends Model
{
    /** @use HasFactory<\Database\Factories\TicketFactory> */
    use HasFactory, SoftDeletes;

    // Fillable attributes for mass assignment
    protected $fillable = [
        'order_id',
        'temporary_order_id',
        'ticket_type_id',
        'qr_code',
        'scanned_at',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function temporaryOrder()
    {
        return $this->belongsTo(TemporaryOrder::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class,  'ticket_type_id');
    }
}

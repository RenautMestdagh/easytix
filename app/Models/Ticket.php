<?php

namespace App\Models;

use App\Models\Scopes\TicketOrganizationScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[ScopedBy([TicketOrganizationScope::class])]
class Ticket extends Model
{
    /** @use HasFactory<\Database\Factories\TicketFactory> */
    use HasFactory;

    // Fillable attributes for mass assignment
    protected $fillable = [
        'order_id',
        'temporary_order_id',
        'ticket_type_id',
        'qr_code',
        'scanned_at',
        'scanned_by',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            // Generate QR code only if it's not already set
            if (!$ticket->qr_code) {
                do {
                    $qrCode = substr(str_replace('-', '', Str::uuid()), 0, 12);
                } while (static::query()->where('qr_code', $qrCode)->exists());

                $ticket->qr_code = $qrCode;
            }
        });
    }


    public function order()
    {
        return $this->belongsTo(Order::class,  'order_id');
    }

    public function temporaryOrder()
    {
        return $this->belongsTo(TemporaryOrder::class,  'temporary_order_id');
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class,  'ticket_type_id');
    }

    public function scannedByUser()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}

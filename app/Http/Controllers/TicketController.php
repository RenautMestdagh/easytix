<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;

class TicketController extends Controller
{
    //
    public function download(Request $request, $subdomain, $orderId)
    {
        // Load relationships
        $order = Order::with([
            'tickets.ticketType.event.organization',
            'customer'
        ])->findOrFail($orderId);

        // Generate the PDF with proper page settings
        return Pdf::html(view('tickets.download', [
            'order' => $order,
            'tickets' => $order->tickets,
        ]))
            ->format('A4')
            ->orientation('portrait')
            ->save("tickets-order-{$order->id}.pdf")
            ->download();
    }
}

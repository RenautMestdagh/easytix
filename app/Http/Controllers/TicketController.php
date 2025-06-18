<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Facades\Pdf;

class TicketController extends Controller
{
    //
    public function download(Request $request, $subdomain, $orderUniqid)
    {
        // Load relationships
        $order = Order::with([
            'tickets.ticketType.event.organization',
            'customer'
        ])->where('uniqid', $orderUniqid)->firstOrFail();

        // Generate QR codes for each ticket
        $ticketsWithQr = $order->tickets->map(function($ticket) {
            $ticket->qr_code_image = QrCode::format('svg')
                ->size(200)
                ->generate($ticket->qr_code);
            return $ticket;
        });

        // Generate the PDF with proper page settings
        return Pdf::view('tickets.download', [
            'order' => $order,
            'tickets' => $ticketsWithQr,
        ])->withBrowsershot(function (Browsershot $browsershot) {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'LIN') {
                $browsershot->setChromePath("/usr/bin/chromium");
            }
        })
            ->format('A4')
            ->orientation('portrait')
            ->download("tickets-order-{$order->uniqid}.pdf");
    }
}

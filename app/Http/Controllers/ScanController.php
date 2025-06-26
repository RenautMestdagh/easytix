<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    use AuthorizesRequests;
    public function show()
    {
        return view('partials.show-scanner');
    }

    public function scan(Request $request)
    {
        $validated = $request->validate([
            'ticket_code' => 'required|string|max:255',
        ]);

        $ticket = Ticket::with(['ticketType.event.organization', 'ticketType', 'scannedByUser'])
            ->where('qr_code', $validated['ticket_code'])
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found'
            ], 404);
        }

        $response = [
            'success' => true,
            'ticket' => [
                'code' => $ticket->qr_code,
                'event' => [
                    'name' => $ticket->ticketType->event->name,
                    'date' => $ticket->ticketType->event->date->format('M j, Y g:i A'),
                    'organization' => $ticket->ticketType->event->organization->name,
                ],
                'ticket_type' => [
                    'name' => $ticket->ticketType->name,
                    'price' => '$' . number_format($ticket->ticketType->price_cents / 100, 2),
                ],
                'scanned' => (bool)$ticket->scanned_at,
                'scanned_at' => $ticket->scanned_at?->format('M j, Y g:i A'),
                'scanned_by' => $ticket->scannedByUser?->name,
            ]
        ];

        // If not already scanned, mark as scanned
        if (!$ticket->scanned_at) {
            $ticket->update([
                'scanned_at' => now(),
                'scanned_by' => auth()->id()
            ]);
        }

        return response()->json($response);
    }
}

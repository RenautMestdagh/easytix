<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScanController extends Controller
{
    use AuthorizesRequests;

    public function show()
    {
        $events = Event::where('date', '>=', now()->format('Y-m-d')) // Only show upcoming/current events
            ->orderBy('date')
            ->get();

        return view('partials.show-scanner', compact('events'));
    }

    public function scan(Request $request)
    {
        $validated = $request->validate([
            'ticket_code' => 'required|string|max:255',
            'event_id' => [
                'required',
                'exists:events,id',
                function ($attribute, $value, $fail) {
                    if (session('organization_id') !== Event::findOrFail($value)->organization_id) {
                        $fail('The event does not belong to your organization');
                    }
                },
            ]
        ]);

        $ticket = Ticket::with(['ticketType.event.organization', 'ticketType', 'scannedByUser'])
            ->where('qr_code', $validated['ticket_code'])
            ->whereHas('ticketType', function($query) use ($validated) {
                $query->where('event_id', $validated['event_id']);
            })
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found for this event',
                'ticket' => null
            ]);
        }

        // Rest of your scan method remains the same...
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
                    'price' => '€' . number_format($ticket->ticketType->price_cents / 100, 2),
                ],
                'scanned' => (bool)$ticket->scanned_at,
                'scanned_at' => $ticket->scanned_at?->format('M j, Y g:i A'),
                'scanned_by' => $ticket->scannedByUser?->name,
            ]
        ];

        if (!$ticket->scanned_at) {
            $retries = 0;
            $maxRetries = 3;
            $updated = false;

            while ($retries < $maxRetries && !$updated) {
                try {
                    $ticket->update([
                        'scanned_at' => now(),
                        'scanned_by' => auth()->id()
                    ]);
                    $updated = true;
                } catch (\Exception $e) {
                    $retries++;
                    if ($retries >= $maxRetries) {
                        Log::error('Failed to update scanned state for ticket after ' . $maxRetries . ' attempts: ' . $e->getMessage());
                    } else {
                        usleep(100000);
                    }
                }
            }
        }

        return response()->json($response);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Models\Ticket;

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

        $ticket = Ticket::where('qr_code', $validated['ticket_code'])->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found'
            ], 404);
        }

        // Check if ticket is already scanned
        if ($ticket->scanned_at) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket already scanned',
                'scanned_at' => $ticket->scanned_at,
                'scanned_by' => User::find($ticket->scanned_by)->name
            ]);
        }

        // Process the scan
        $ticket->update([
            'is_scanned' => true,
            'scanned_at' => now(),
            'scanned_by' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'ticket' => $ticket,
            'scanned_by' => auth()->user()->name
        ]);
    }
}

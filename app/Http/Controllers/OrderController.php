<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Event;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function show(Request $request, $subdomain, $orderUniqid)
    {
        $order = Order::with(['tickets.ticketType.event'])
            ->where('uniqid', $orderUniqid)
            ->firstOrFail();

        // Ensure the order belongs to the subdomain (or organization) you are currently visiting
        if (!$order->event) {
            abort(404);
        }

        // Calculate quantities for each ticket type
        $quantities = [];
        foreach ($order->tickets as $ticket) {
            $ticketTypeId = $ticket->ticket_type_id;
            if (!isset($quantities[$ticketTypeId])) {
                $quantities[$ticketTypeId] = (object)[
                    'name' => $ticket->ticketType->name,
                    'amount' => 0,
                    'price_cents' => $ticket->ticketType->price_cents
                ];
            }
            $quantities[$ticketTypeId]->amount++;
        }

        $orderTotal = collect($quantities)->sum(function($ticket) {
            return $ticket->price_cents * $ticket->amount;
        });

        return view('partials.order', [
            'order' => $order,
            'event' => $order->event,
            'quantities' => $quantities,
            'orderTotal' => $orderTotal,
        ])->layout('components.layouts.organization', [
            'backgroundOverride' => $order->event->background_image_url ?? null,
            'logoOverride' => $order->event->header_image_url ?? null,
            'organization' => $order->event->organization // Make sure to pass the organization
        ]);
    }
}

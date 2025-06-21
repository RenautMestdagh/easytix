<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Event;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function show(Request $request, $subdomain, $orderUniqid)
    {
        $order = Order::with(['tickets.ticketType.event', 'discountCodes'])
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

        $subtotal = collect($quantities)->sum(function($ticket) {
            return $ticket->price_cents * $ticket->amount;
        });

        // Calculate discount amount
        $discountAmount = 0;
        if ($order->discountCodes->isNotEmpty()) {
            $sumPercentage = $order->discountCodes->sum('discount_percent');
            $sumFixed = $order->discountCodes->sum('discount_fixed_cents');

            if ($sumPercentage > 0) {
                $discountAmount = $subtotal * ($sumPercentage / 100);
            } elseif ($sumFixed > 0) {
                $discountAmount = $sumFixed;
            }
        }

        $orderTotal = max(0, $subtotal - $discountAmount);

        return view('partials.order', [
            'order' => $order,
            'event' => $order->event,
            'quantities' => $quantities,
            'subtotal' => $subtotal,
            'discountAmount' => $discountAmount,
            'orderTotal' => $orderTotal,
            'appliedDiscounts' => $order->discountCodes,
        ]);
    }
}

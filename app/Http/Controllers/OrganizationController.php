<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function show(Request $request)
    {
        $events = Event::with(['venue', 'publishedTicketTypes' => function($query) {
            $query->withCount('tickets');
        }])
            ->withCount('tickets')
            ->where('is_published', true)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date')
            ->get();

        foreach ($events as $event) {
            $event->sold_out;
            if($event->capacity && $event->tickets_count >= $event->capacity) {
                $event->sold_out = true;
            } else {
                // Check if all published ticket types are sold out
                $allSoldOut = true;
                foreach ($event->publishedTicketTypes as $ticketType) {
                    if ($ticketType->available_quantity === null || $ticketType->tickets_count < $ticketType->available_quantity) {
                        $allSoldOut = false;
                        break;
                    }
                }
                $event->sold_out = $allSoldOut && count($event->publishedTicketTypes) > 0;
            }
        }

        return view('frontend.organization-home', compact('events'));
    }
}

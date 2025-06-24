<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function show(Request $request)
    {
        $events = Event::with( 'venue', 'publishedTicketTypes')->where('is_published', true)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date')
            ->get();

        return view('frontend.homepage', compact('events'));
    }
}

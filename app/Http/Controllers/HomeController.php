<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'eventCount' => Event::withoutGlobalScopes()->where('is_published', true)->count(),
            'ticketCount' => Ticket::withoutGlobalScopes()->count(),
            'featuredEvents' => Event::withoutGlobalScopes()->without('ticketTypes')->where('is_published', true)
                ->with(['organization' => function ($query) {
                    $query->withoutGlobalScopes();
                }, 'venue' => function ($query) {
                    $query->withoutGlobalScopes();
                }, 'ticketTypes' => function ($query) {
                    $query->withoutGlobalScopes();
                }])
                ->orderByDesc('created_at')
                ->take(3)
                ->get()
        ]);
    }
}

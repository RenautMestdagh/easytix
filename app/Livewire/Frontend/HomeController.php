<?php

namespace App\Livewire\Frontend;

use App\Models\Event;
use App\Models\Ticket;
use Livewire\Component;

class HomeController extends Component
{
    public $eventCount;
    public $ticketCount;
    public $featuredEvents;

    public function mount()
    {
        $this->eventCount = Event::withoutGlobalScopes()->where('is_published', true)->count();
        $this->ticketCount = Ticket::withoutGlobalScopes()->count();
        $this->featuredEvents = Event::withoutGlobalScopes()->without('ticketTypes')
            ->where('is_published', true)
            ->where('date', '>=', now()->startOfDay())
            ->with([
                'organization' => function ($query) {
                    $query->withoutGlobalScopes();
                },
                'venue' => function ($query) {
                    $query->withoutGlobalScopes();
                },
                'ticketTypes' => function ($query) {
                    $query->withoutGlobalScopes();
                }
            ])
            ->orderBy('date', 'asc')
            ->take(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.frontend.home', [
            'eventCount' => $this->eventCount,
            'ticketCount' => $this->ticketCount,
            'featuredEvents' => $this->featuredEvents,
        ])->layout('components.layouts.home');
    }
}

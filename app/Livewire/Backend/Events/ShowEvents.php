<?php

namespace App\Livewire\Backend\Events;

use App\Models\Event;
use App\Models\Organization;
use App\Traits\FlashMessage;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class ShowEvents extends Component
{
    use WithPagination, FlashMessage;

    public $includeDeleted = false;
    public $search = '';
    public $startDate;
    public $endDate;
    public $statusFilter = 'all';
    public $venueFilter = null;
    public $sortField = 'date';
    public $sortDirection = 'asc';
    public $perPage = 10;

    protected $listeners = ['venueSelected' => 'venueFilterSelected',];
    public function mount()
    {
        $this->startDate = now()->format('Y-m-d');
    }

    public function getEventsProperty()
    {
        return Event::query()
            ->with(['organization', 'venue'])
            ->withCount(['tickets'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->startDate, function ($query) {
                $query->where('date', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->where('date', '<=', $this->endDate . ' 23:59:59');
            })
            ->when($this->venueFilter, function ($query) {
                $query->where('venue_id', $this->venueFilter);
            })
            ->when($this->statusFilter === 'published', function ($query) {
                $query->where('is_published', true);
            })
            ->when($this->statusFilter === 'draft', function ($query) {
                $query->where('publish_at', null)->where('is_published', false);
            })
            ->when($this->statusFilter === 'scheduled', function ($query) {
                $query->whereNot('publish_at', null)->where('is_published', false);
            })
            ->when($this->includeDeleted, function ($query) {
                $query->withTrashed();
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedIncludeDeleted()
    {
        $this->resetPage();
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function venueFilterSelected($venueId, $venueName)
    {
        $this->venueFilter = $venueId;
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function editEvent(Event $event)
    {
        session(['events.edit.referrer' => request()->headers->get('referer')]);
        return redirect()->route('events.update', ['event' => $event->id]);
    }

    public function deleteEvent($id)
    {
        $this->authorize('events.delete');
        try {
            Event::findOrFail($id)->delete();
            $this->flashMessage('Event deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting event: ' . $e->getMessage());
            $this->flashMessage('Error while deleting event.', 'error');
        }
    }

    public function forceDeleteEvent($id)
    {
        $this->authorize('events.delete');

        try {
            $event = Event::withTrashed()->findOrFail($id);
            $ticketTypes = $event->ticketTypes;

            foreach ($ticketTypes as $ticketType)
                if($ticketType->allTickets->count())
                    return $this->flashMessage('Cannot permanently delete event with (reserved) tickets.', 'error');

            $event->forceDelete();
            $this->flashMessage('Event deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error permanently deleting event: ' . $e->getMessage());
            $this->flashMessage('Error while deleting event.', 'error');
        }
    }

    public function restoreEvent($id)
    {
        $this->authorize('events.delete');

        try {
            Event::withTrashed()->findOrFail($id)->restore();
            $this->flashMessage('Event restored successfully.');
        } catch (\Exception $e) {
            Log::error('Error restoring event: ' . $e->getMessage());
            $this->flashMessage('Error while restoring event.', 'error');
        }
    }

    public function render()
    {
        return view('livewire.backend.events.show-events', [
            'events' => $this->events,
            'organizations' => Organization::all(),
        ]);
    }
}

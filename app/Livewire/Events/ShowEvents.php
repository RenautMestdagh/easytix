<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\Organization;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ShowEvents extends Component
{
    use WithPagination;

    public $includeDeleted = false;
    public $search = '';
    public $selectedOrganization = '';
    public $startDate;
    public $endDate;
    public $statusFilter = 'all';
    public $sortField = 'date';
    public $sortDirection = 'asc';
    public $perPage = 10;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->authorize('events.read');
        $this->startDate = now()->format('Y-m-d');

        // Set default organization for superadmin if none selected
        if (auth()->user()->hasRole('superadmin') && empty($this->selectedOrganization)) {
            $firstOrg = Organization::first();
            if ($firstOrg) {
                $this->selectedOrganization = $firstOrg->id;
            }
        }
    }

    public function getEventsProperty()
    {
        return Event::query()
            ->with(['organization', 'ticketTypes', 'tickets'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('location', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedOrganization && $this->selectedOrganization !== 'all', function ($query) {
                $query->where('organization_id', $this->selectedOrganization);
            })
            ->when($this->startDate, function ($query) {
                $query->where('date', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->where('date', '<=', $this->endDate . ' 23:59:59');
            })
            ->when($this->statusFilter === 'published', function ($query) {
                $query->where('is_published', true);
            })
            ->when($this->statusFilter === 'draft', function ($query) {
                $query->where('is_published', false);
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

    public function updatedSelectedOrganization()
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

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function deleteEvent($id)
    {
        $this->authorize('events.delete');

        DB::transaction(function () use ($id) {
            $event = Event::findOrFail($id);
            $event->delete();

            session()->flash('message', __('Event deleted successfully.'));
            $this->dispatch('flash-message');
        });
    }

    public function forceDeleteEvent($id)
    {
        $this->authorize('events.delete');
        $event = Event::withTrashed()->findOrFail($id);
        $event->forceDelete();

        session()->flash('message', __('Event permanently deleted.'));
        $this->dispatch('flash-message');
    }

    public function restoreEvent($id)
    {
        $this->authorize('events.delete');
        $event = Event::withTrashed()->findOrFail($id);
        $event->restore();

        session()->flash('message', 'Event restored successfully.');
        $this->dispatch('flash-message');
    }

    public function render()
    {
        return view('livewire.events.show-events', [
            'events' => $this->events,
            'organizations' => Organization::all(),
        ]);
    }
}

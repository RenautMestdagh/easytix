<?php

namespace App\Livewire\Backend\Venues;

use App\Models\Organization;
use App\Models\Venue;
use Livewire\Component;
use Livewire\WithPagination;

class ShowVenues extends Component
{
    use WithPagination;

    public $includeDeleted = false;
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // ShowVenues.php - update the getVenuesProperty method
    public function getVenuesProperty()
    {
        return Venue::query()
            ->with(['organization'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('venues.name', 'like', '%' . $this->search . '%')
                        ->orWhere('venues.max_capacity', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->includeDeleted, function ($query) {
                $query->withTrashed();
            })
            ->orderBy('venues.' . $this->sortField, $this->sortDirection)
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

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.venues.show-venues', [
            'venues' => $this->venues,
            'organizations' => Organization::all(),
        ]);
    }

    public function deleteVenue($id)
    {
        $venue = Venue::findOrFail($id);
        $venue->delete();

        session()->flash('message', __('Venue deleted successfully.'));
        $this->dispatch('flash-message');
    }

    public function forceDeleteVenue($id)
    {
        $venue = Venue::withTrashed()->findOrFail($id);
        $venue->forceDelete();

        session()->flash('message', __('Venue permanently deleted.'));
        $this->dispatch('flash-message');
    }

    public function restoreVenue($id)
    {
        $venue = Venue::withTrashed()->findOrFail($id);
        $venue->restore();

        session()->flash('message', 'Venue restored successfully.');
        $this->dispatch('flash-message');
    }
}

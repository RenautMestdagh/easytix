<?php

namespace App\Livewire\Backend\Venues;

use App\Models\Organization;
use App\Models\Venue;
use App\Traits\FlashMessage;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class ShowVenues extends Component
{
    use WithPagination, FlashMessage;

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
        return view('livewire.backend.venues.show-venues', [
            'venues' => $this->venues,
            'organizations' => Organization::all(),
        ]);
    }

    public function deleteVenue($id)
    {
        $this->authorize('venues.delete');
        try{
            Venue::findOrFail($id)->delete();
            $this->flashMessage('Venue deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting venue: ' . $e->getMessage());
            $this->flashMessage('Error while deleting venue.', 'error');
        }
    }

    public function forceDeleteVenue($id)
    {
        $this->authorize('venues.delete');
        try {
            Venue::withTrashed()->findOrFail($id)->forceDelete();
            $this->flashMessage('Venue permanently deleted.');
        } catch (\Exception $e) {
            Log::error('Error permanently deleting venue: ' . $e->getMessage());
            $this->flashMessage('Error while permanently deleting venue.', 'error');
        }
    }

    public function restoreVenue($id)
    {
        $this->authorize('venues.delete');
        try {
            Venue::withTrashed()->findOrFail($id)->restore();
            $this->flashMessage('Venue restored successfully.');
        } catch (\Exception $e) {
            Log::error('Error restoring venue: ' . $e->getMessage());
            $this->flashMessage('Error restoring venue.', 'error');
        }
    }
}

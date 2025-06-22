<?php

namespace App\Livewire\Backend\Organizations;

use App\Models\Organization;
use App\Traits\FlashMessage;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class ShowOrganizations extends Component
{
    use WithPagination, FlashMessage;

    public $includeDeleted = false; // Flag to include soft-deleted organizations
    public $search = '';
    public $sortField = 'name'; // Default sort field
    public $sortDirection = 'asc'; // Default sort direction
    public $perPage = 10; // Add a property for pagination limit


    public function mount()
    {
    }

    public function render()
    {
        // Eager load the count of users and events for each organization and paginate
        $organizations = Organization::withCount('users')
            ->withCount('events')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('subdomain', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->includeDeleted, function ($query) {
                $query->withTrashed(); // Include soft-deleted organizations
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $this->resetPage();

        return view('livewire.backend.organizations.show-organizations', compact('organizations'));
    }

    public function sortBy($field)
    {
        $this->resetPage();
        // If it's the same field, toggle the direction
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // Set the field and default to 'asc' unless it's users_count or events_count
            $this->sortField = $field;

            if ($field === 'users_count' || $field === 'events_count') {
                // Default direction for these fields should be 'desc'
                $this->sortDirection = 'desc';
            } else {
                // Default direction for other fields is 'asc'
                $this->sortDirection = 'asc';
            }
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }


    public function deleteOrganization($id)
    {
        $this->authorize('organizations.delete');

        try {
            Organization::findOrFail($id)->delete();
            $this->flashMessage('Organization deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting organization: ' . $e->getMessage());
            $this->flashMessage('Error while deleting organization.', 'error');
        }
    }

    public function restoreOrganization($id)
    {
        $this->authorize('organizations.delete');

        try {
            Organization::withTrashed()->findOrFail($id)->restore();
            $this->flashMessage('Organization restored successfully.');
        } catch (\Exception $e) {
            Log::error('Error restoring organization: ' . $e->getMessage());
            $this->flashMessage('Error while restoring organization.', 'error');
        }
    }

    public function forceDeleteOrganization($id)
    {
        $this->authorize('organizations.delete');

        try {
            $organization = Organization::withTrashed()->findOrFail($id);
            if ($organization->ticket_count > 0) {
                $this->flashMessage('Cannot permanently delete organization that has tickets.', 'error');
                return;
            }

            $organization->forceDelete();
            $this->flashMessage('Organization permanently deleted.');
        } catch (\Exception $e) {
            Log::error('Error permanently deleting organization: ' . $e->getMessage());
            $this->flashMessage('Error while permanently deleting organization.', 'error');
        }
    }
}

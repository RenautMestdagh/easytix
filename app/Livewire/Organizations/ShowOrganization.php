<?php

namespace App\Livewire\Organizations;

use App\Models\Organization;
use Livewire\Component;
use Livewire\WithPagination;

class ShowOrganization extends Component
{
    use WithPagination;

    public $includeDeleted = false; // Flag to include soft-deleted organizations
    public $search = '';
    public $sortField = 'name'; // Default sort field
    public $sortDirection = 'asc'; // Default sort direction
    public $perPage = 10; // Add a property for pagination limit


    public function render()
    {
        // Eager load the count of users for each organization and paginate
        $organizations = Organization::withCount('users')
            ->withCount('events')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->includeDeleted, function ($query) {
                $query->withTrashed(); // Include soft-deleted organizations
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $this->resetPage();

        return view('livewire.organizations.show-organizations', compact('organizations'));
    }

    public function sortBy($field)
    {
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


    public function edit($id)
    {
        return redirect()->route('organizations.edit', $id);
    }


    public function deleteOrganization($id)
    {
        $organization = Organization::findOrFail($id);

        // Delete the organization
        $organization->delete();

        // Optionally, add a success message
        session()->flash('message', 'Organization deleted successfully.');
        $this->dispatch('flash-message');
    }

    public function restoreOrganization($id)
    {
        $organization = Organization::withTrashed()->findOrFail($id);
        $organization->restore(); // Restore the soft-deleted organization

        session()->flash('message', 'Organization restored successfully.');
        $this->dispatch('flash-message');
    }

    public function forceDeleteOrganization($id)
    {
        $organization = Organization::withTrashed()->findOrFail($id);
        $organization->forceDelete();

        session()->flash('message', 'Organization permanently deleted.');
        $this->dispatch('flash-message');
    }
}

<?php

namespace App\Livewire\Users;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ShowUsers extends Component
{
    use WithPagination;

    public $includeDeleted = false; // Flag to include soft-deleted organizations
    public $search = '';
    public $sortField = 'name'; // Default sort field
    public $sortDirection = 'asc'; // Default sort direction
    public $selectedOrganization;
    public $selectedRole;
    public $perPage = 10; // Add a property for pagination limit

    public function mount()
    {
        $this->authorize('users.read');
    }

    public function getUsersProperty()
    {
        return User::query()
            ->with(['roles', 'organization'])
            ->leftJoin('organizations', 'users.organization_id', '=', 'organizations.id')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('users.name', 'like', '%' . $this->search . '%')
                        ->orWhere('users.email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedOrganization && $this->selectedOrganization !== 'all', function ($query) {
                $query->where('users.organization_id', $this->selectedOrganization);
            })
            ->when($this->selectedRole && $this->selectedRole !== 'all', function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', $this->selectedRole);
                });
            })
            ->when($this->includeDeleted, function ($query) {
                $query->withTrashed();
            })
            ->orderBy(
                $this->sortField === 'organization_id' ? 'organizations.name' : 'users.' . $this->sortField,
                $this->sortDirection
            )
            ->select('users.*')
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

    public function getOrganizationsWithSingleAdmin(): array
    {
        return User::select('organization_id')
            ->whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })
            ->groupBy('organization_id')
            ->havingRaw('COUNT(*) = 1')
            ->pluck('organization_id')
            ->toArray();
    }



    public function render()
    {
        return view('livewire.users.show-users', [
            'users' => $this->users,
            'organizations' => Organization::all(),
            'singleAdminOrgIds' => $this->getOrganizationsWithSingleAdmin(),
        ]);
    }

    public function deleteUser($id)
    {
        $this->authorize('users.delete');
        if (auth()->id() === (int) $id) {
            session()->flash('message_type', 'error');
            session()->flash('message', __('You cannot delete your own account.'));
            $this->dispatch('flash-message');
            return;
        }

        DB::transaction(function () use ($id) {
            $user = User::findOrFail($id);

            if ($user->organization_id) {
                $organization = Organization::findOrFail($user->organization_id);

                // Lock all admin user rows for update to prevent race conditions
                $adminIds = $organization->admins()->lockForUpdate()->pluck('id');

                if ($adminIds->count() === 1 && $adminIds->first() === $user->id) {
                    session()->flash('message_type', 'error');
                    session()->flash('message', __('Cannot delete the last admin in the organization.'));
                    $this->dispatch('flash-message');
                    return;
                }

                $user->delete();

                session()->flash('message', __('User deleted successfully.'));
                $this->dispatch('flash-message');
                return;
            }

            // Handle the case where organization_id is null
            $usersWithNoOrganization = User::whereNull('organization_id')->lockForUpdate()->get();

            if ($usersWithNoOrganization->count() > 1) {
                $user->delete();

                session()->flash('message', __('User deleted successfully.'));
                $this->dispatch('flash-message');
            } else {
                session()->flash('message_type', 'error');
                session()->flash('message', __('Cannot delete the last superadmin.'));
                $this->dispatch('flash-message');
            }
        });
    }

    public function forceDeleteUser($id)
    {
        $this->authorize('users.delete');
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete();

        session()->flash('message', __('User permanently deleted.'));
        $this->dispatch('flash-message');
    }

    public function restoreUser($id)
    {
        $this->authorize('users.delete');
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        session()->flash('message', 'User restored successfully.');
        $this->dispatch('flash-message');
    }
}

<?php

namespace App\Livewire\Users;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class ShowUsers extends Component
{
    use WithPagination;

    public $includeDeleted = false; // Flag to include soft-deleted organizations
    public $search = '';
    public $sortField = 'name'; // Default sort field
    public $sortDirection = 'asc'; // Default sort direction
    public $selectedOrganization;
    public $roles = []; // Define roles property
    public $selectedRole;
    public $perPage = 10; // Add a property for pagination limit

    public function mount()
    {
        $this->authorize('users.read');

        // Get all roles
        $roles = Role::all()->pluck('name', 'name')->toArray();

        // Remove 'superadmin' if the user doesn't have it
        if (!auth()->user()->hasRole('superadmin')) {
            unset($roles['superadmin']);
        }

        $this->roles = $roles;
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

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedSelectedRole($value)
    {
        $this->resetPage();
        if ($value === 'superadmin') {
            $this->selectedOrganization = '';
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
            'roles' => $this->roles,
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

    public function loginAsUser($userId)
    {
//        $this->authorize('login-as.use'); // This checks for the specific permission

        $targetUser = User::findOrFail($userId);

        // Check if trying to login as self or another superadmin
        if (auth()->id() === $userId) {
            session()->flash('message_type', 'error');
            session()->flash('message', __('You cannot login as yourself.'));
            $this->dispatch('flash-message');
            return;
        }

        if ($targetUser->hasRole('superadmin')) {
            session()->flash('message_type', 'error');
            session()->flash('message', __('Cannot login as another superadmin.'));
            $this->dispatch('flash-message');
            return;
        }

        // Store the original user ID in session so we can switch back
        session()->put('original_user_id', auth()->id());

        // Login as the target user
        auth()->login($targetUser);

        session()->flash('message', __('Now logged in as :name', ['name' => $targetUser->name]));
        $this->dispatch('flash-message');

        return redirect()->route('dashboard'); // Redirect to dashboard or desired route
    }

    public function switchBackToOriginalUser()
    {
        if (!session()->has('original_user_id')) {
            session()->flash('message_type', 'error');
            session()->flash('message', __('No original user to switch back to.'));
            $this->dispatch('flash-message');
            return;
        }

        $originalUser = User::findOrFail(session('original_user_id'));

        auth()->login($originalUser);
        session()->forget('original_user_id');
        session()->forget('organization_id');

        session()->flash('message', __('Switched back to your original account.'));
        $this->dispatch('flash-message');

        return redirect()->route('users.index');
    }
}

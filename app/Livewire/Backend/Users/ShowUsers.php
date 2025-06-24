<?php

namespace App\Livewire\Backend\Users;

use App\Models\Organization;
use App\Models\User;
use App\Traits\FlashMessage;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ShowUsers extends Component
{
    use WithPagination, FlashMessage;

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
            ->leftJoin('organizations', function($join) {
                $join->on('users.organization_id', '=', 'organizations.id')
                    ->whereNull('organizations.deleted_at');
            })
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

    public function deleteUser($id)
    {
        $this->authorize('users.delete');

        if (auth()->id() === (int) $id) {
            $this->flashMessage('You cannot delete your own account.', 'error');
            return;
        }

        try{
            User::findOrFail($id)->delete();
            $this->flashMessage('User deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting User: ' . $e->getMessage());
            $this->flashMessage('Error while deleting User.', 'error');
        }
    }

    public function forceDeleteUser($id)
    {
        $this->authorize('users.delete');
        try {
            User::withTrashed()->findOrFail($id)->forceDelete();
            $this->flashMessage('User permanently deleted.');
        } catch (\Exception $e) {
            Log::error('Error permanently deleting user: ' . $e->getMessage());
            $this->flashMessage('Error while permanently deleting user.', 'error');
        }
    }

    public function restoreUser($id)
    {
        $this->authorize('users.delete');
        try{
            User::withTrashed()->findOrFail($id)->restore();
            $this->flashMessage('User restored successfully.');
        } catch (\Exception $e) {
            Log::error('Error restoring user: ' . $e->getMessage());
            $this->flashMessage('Error restoring user.', 'error');
        }
    }

    public function render()
    {
        return view('livewire.backend.users.show-users', [
            'users' => $this->users,
            'roles' => $this->roles,
            'organizations' => Organization::all(),
            'singleAdminOrgIds' => $this->getOrganizationsWithSingleAdmin(),
        ]);
    }

    public function loginAsUser($userId)
    {
        $this->authorize('login-as.use');

        $targetUser = User::findOrFail($userId);

        if (auth()->id() === $userId)
            return $this->flashMessage(__('You cannot login as yourself.'),  'error');
        if ($targetUser->hasRole('superadmin'))
            return $this->flashMessage(__('Cannot login as superadmin.'), 'error');
        if ($targetUser->deleted_at)
            return $this->flashMessage(__('Cannot login as deleted user.'), 'error');

        // Store the original user ID in session so we can switch back
        session()->put('original_user_id', auth()->id());

        // Login as the target user
        auth()->login($targetUser);
        $this->flashMessage(__('Now logged in as :name', ['name' => $targetUser->name]));

        return redirect()->route('dashboard');
    }

    public function switchBackToOriginalUser()
    {
        if (!session()->has('original_user_id')) {
            throw new AccessDeniedHttpException;
        }

        $originalUser = User::findOrFail(session('original_user_id'));

        auth()->login($originalUser);
        session()->forget('original_user_id');
        session()->forget('organization_id');

        $this->flashMessage('Switched back to your original account.');

        return redirect()->route('users.index');
    }
}

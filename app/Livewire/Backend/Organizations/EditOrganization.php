<?php

namespace App\Livewire\Backend\Organizations;

use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class EditOrganization extends Component
{
    use WithPagination;

    public Organization $organization;
    public $organizationName = '';
    public $organizationSubdomain = '';

    public $adminCount;
    public $saveButtonVisible = false;

    // For users table
    public $includeDeletedUsers = false;
    public $userSearch = '';
    public $userRole = '';
    public $userSortField = 'name';
    public $userSortDirection = 'asc';
    public $perPage = 10;

    public function mount(Organization $organization)
    {
        $this->authorize('organizations.update', $organization);

        $this->organization = $organization;

        $this->organizationName = $organization->name;
        $this->organizationSubdomain = $organization->subdomain;

        $this->adminCount = $this->organization->admins()->count();
    }

    public function updated($propertyName): void
    {
        $this->resetErrorBag($propertyName);

        $fieldRules = (new UpdateOrganizationRequest($this->organization->id))->rules();
        $fieldMessages = (new UpdateOrganizationRequest())->messages();

        if (!array_key_exists($propertyName, $fieldRules)) {
            return; // skip validation if no rule is defined
        }

        $this->saveButtonVisible = $this->organization->name !== $this->organizationName || $this->organization->subdomain !== $this->organizationSubdomain;

        $this->validateOnly($propertyName, $fieldRules, $fieldMessages);
    }

    public function save()
    {
        $this->authorize('organizations.update', $this->organization);

        // Skip validation for subdomain if it's unchanged
        $rules = (new UpdateOrganizationRequest($this->organization->id))->rules();
        $messages = (new UpdateOrganizationRequest())->messages();

        $validated = $this->validate($rules, $messages);

        $oldSubdomain = $this->organization->subdomain;
        $newSubdomain = $validated['organizationSubdomain'];

        try {
            $this->organization->update([
                'name' => $validated['organizationName'],
                'subdomain' => $validated['organizationSubdomain'],
            ]);

            session()->flash('message', __('Organization successfully updated.'));
            $this->dispatch('flash-message');

            $this->saveButtonVisible = false;

            // If the subdomain has changed, redirect to the new subdomain's edit page
            if ($oldSubdomain !== $newSubdomain && session('organization_id')) {
                $baseUrl = config('app.url');
                $hostParts = parse_url($baseUrl);

                $scheme = $hostParts['scheme'] ?? 'http';
                $domain = $hostParts['host'] ?? 'localhost';
                $port = isset($hostParts['port']) ? ':' . $hostParts['port'] : '';

                // Construct new host: newsub.localeasytix.org
                $newHost = $newSubdomain . '.' . $domain;

                // Build relative path to edit page
                $path = route('organizations.edit', $this->organization->id, false);

                // Full new URL with port if present
                $newUrl = $scheme . '://' . $newHost . $port . $path;

                return redirect()->away($newUrl);
            }

        } catch (\Exception $e) {
            session()->flash('message', __('An error occurred while updating the organization.'));
            session()->flash('message_type', 'error');
            $this->dispatch('flash-message');
        }
    }

    public function sortUsersBy($field)
    {
        $this->resetPage();
        if ($this->userSortField === $field) {
            $this->userSortDirection = $this->userSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->userSortField = $field;
            $this->userSortDirection = 'asc';
        }
    }

    public function getUsersProperty()
    {
        return $this->organization->users()
            ->with('roles')
            ->when($this->userSearch, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->userSearch . '%')
                        ->orWhere('email', 'like', '%' . $this->userSearch . '%');
                });
            })
            ->when($this->userRole && $this->userRole !== 'all', function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', $this->userRole);
                });
            })
            ->when($this->includeDeletedUsers, function ($query) {
                $query->withTrashed();
            })
            ->orderBy($this->userSortField, $this->userSortDirection)
            ->paginate($this->perPage);
    }

    public function updatedUserSearch()
    {
        $this->resetPage();
    }

    public function updatedUserRole()
    {
        $this->resetPage();
    }


    public function render()
    {
        return view('livewire.organizations.edit-organization', [
            'users' => $this->users,
            'adminCount' => $this->adminCount,
        ]);
    }

    public function removeUser($id)
    {
        $this->authorize('users.delete', $this->organization);
        DB::transaction(function () use ($id) {
            $user = User::findOrFail($id);
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

            $this->adminCount = $this->organization->admins()->count();

            session()->flash('message', __('User deleted successfully.'));
            $this->dispatch('flash-message');
        });
    }

    public function forceDeleteUser($id)
    {
        $this->authorize('users.delete', $this->organization);
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete();

        session()->flash('message', __('User permanently deleted.'));
        $this->dispatch('flash-message');
    }

    public function restoreUser($id)
    {
        $this->authorize('users.delete', $this->organization);
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        $this->adminCount = $this->organization->admins()->count();

        session()->flash('message', 'User restored successfully.');
        $this->dispatch('flash-message');
    }
}

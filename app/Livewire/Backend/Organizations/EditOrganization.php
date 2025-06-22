<?php

namespace App\Livewire\Backend\Organizations;

use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Models\Organization;
use App\Models\User;
use App\Traits\FlashMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class EditOrganization extends Component
{
    use WithPagination, FlashMessage;

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
        $this->organization = $organization;

        $this->organizationName = $organization->name;
        $this->organizationSubdomain = $organization->subdomain;

        $this->adminCount = $this->organization->admins()->count();
    }

    public function updated($propertyName): void
    {
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
        $validated = $this->validate(
            (new UpdateOrganizationRequest($this->organization->id))->rules(),
            (new UpdateOrganizationRequest())->messages(),
        );

        try {
            $this->organization->update([
                'name' => $validated['organizationName'],
                'subdomain' => $validated['organizationSubdomain'],
            ]);
            $this->saveButtonVisible = false;
            $this->flashMessage('Organization successfully updated.');

        } catch (\Exception $e) {
            Log::error('Error updating organization: ' . $e->getMessage());
            $this->flashMessage('Error while updating organization.', 'error');
        }

        // If the subdomain has changed, redirect to the new subdomain's edit page
        $oldSubdomain = $this->organization->subdomain;
        $newSubdomain = $validated['organizationSubdomain'];
        if ($oldSubdomain !== $newSubdomain && session('organization_id')) {
            $baseUrl = config('app.url');
            $hostParts = parse_url($baseUrl);

            $scheme = $hostParts['scheme'] ?? 'http';
            $domain = $hostParts['host'] ?? 'localhost';
            $port = isset($hostParts['port']) ? ':' . $hostParts['port'] : '';

            // Construct new host: newsub.localeasytix.org
            $newHost = $newSubdomain . '.' . $domain;

            // Build relative path to edit page
            $path = route('organizations.update', $this->organization->id, false);

            // Full new URL with port if present
            $newUrl = $scheme . '://' . $newHost . $port . $path;

            redirect()->away($newUrl);
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
        return view('livewire.backend.organizations.edit-organization', [
            'users' => $this->users,
            'adminCount' => $this->adminCount,
        ]);
    }

    public function removeUser($id)
    {
        $this->authorize('users.delete');

        try {
            DB::transaction(function () use ($id) { // Wrap in transaction
                $user = User::findOrFail($id);
                $organization = Organization::findOrFail($user->organization_id);

                // Lock admins (automatically unlocks on commit/rollback)
                $adminIds = $organization->admins()->lockForUpdate()->pluck('id');

                if ($adminIds->count() === 1 && $adminIds->first() === $user->id) {
                    $this->flashMessage('Cannot delete the last admin in the organization.', 'error');
                    return; // Locks released here due to transaction
                }

                $user->delete();
                $this->flashMessage('User deleted successfully.');
                $this->adminCount = $organization->admins()->count();
            });
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            $this->flashMessage('Error while deleting user.', 'error');
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
        try {
            User::withTrashed()->findOrFail($id)->restore();
            $this->adminCount = $this->organization->admins()->count();
            $this->flashMessage('User restored successfully.');
        } catch (\Exception $e) {
            Log::error('Error restoring user: ' . $e->getMessage());
            $this->flashMessage('Error restoring user.', 'error');
        }
    }
}

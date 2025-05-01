<?php

namespace App\Livewire\Organizations;

use App\Http\Requests\Organization\StoreOrganizationRequest;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithPagination;

class EditOrganization extends Component
{
    use WithPagination;

    public Organization $organizationModel;
    public $organization = [
        'name' => '',
        'subdomain' => ''
    ];
    public $saveButtonVisible = false;

    // For users table
    public $includeDeletedUsers = false;
    public $userSearch = '';
    public $userSortField = 'name';
    public $userSortDirection = 'asc';
    public $userPerPage = 10;

    public $adminCount;

    public function mount(Organization $organization)
    {
        $this->organizationModel = $organization;
        $this->organization = [
            'name' => $organization->name,
            'subdomain' => $organization->subdomain
        ];
        $this->adminCount = $organization->users()->where('role', 'admin')->count();
    }

    public function updated($property): void
    {
        $this->resetErrorBag($property);

        $rules = (new StoreOrganizationRequest())->rules();
        $messages = (new StoreOrganizationRequest())->messages();

        if (!array_key_exists($property, $rules)) {
            return; // skip validation if no rule is defined
        }

        // Get value
        $value = data_get($this, $property);

        // Convert 'organization.name' to ['organization' => ['name' => 'value']]
        $data = Arr::undot([$property => $value]);

        if ($this->organizationModel->name !== $this->organization['name'] || $this->organizationModel->subdomain !== $this->organization['subdomain']) {
            $this->saveButtonVisible = true;
        } else {
            $this->saveButtonVisible = false;
        }

        if($this->organizationModel->subdomain === $value) {
            return;
        }

        Validator::make(
            $data,
            [$property => $rules[$property]],
            $messages
        )->validate();
    }

    public function save()
    {
//        if (!Auth::user()->can('edit organizations')) {
//            abort(403);
//        }

        // Skip validation for subdomain if it's unchanged
        $rules = (new UpdateOrganizationRequest())->rules();
        $messages = (new UpdateOrganizationRequest())->messages();

        // If the subdomain is not changed, remove its validation rule
        if ($this->organizationModel->subdomain === $this->organization['subdomain']) {
            unset($rules['organization.subdomain']);
        }

        $validated = $this->validate($rules, $messages);

        try {
            $this->organizationModel->update($validated['organization']);

            session()->flash('message', __('Organization successfully updated.'));
            $this->dispatch('flash-message');

            $this->saveButtonVisible = false;

            return redirect()->route('organizations.index');
        } catch (\Exception $e) {
            session()->flash('message', __('An error occurred while updating the organization.'));
            session()->flash('message_type', 'error');
            $this->dispatch('flash-message');
        }
    }

    public function sortUsersBy($field)
    {
        if ($this->userSortField === $field) {
            $this->userSortDirection = $this->userSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->userSortField = $field;
            $this->userSortDirection = 'asc';
        }
    }

    public function getUsersProperty()
    {
        return $this->organizationModel->users()
            ->when($this->userSearch, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->userSearch . '%')
                        ->orWhere('email', 'like', '%' . $this->userSearch . '%');
                });
            })
            ->when($this->includeDeletedUsers, function ($query) {
                $query->withTrashed();
            })
            ->orderBy($this->userSortField, $this->userSortDirection)
            ->paginate($this->userPerPage);
    }

    public function render()
    {
        return view('livewire.organizations.edit-organization', [
            'users' => $this->users
        ]);
    }

    public function removeUser($id)
    {
        $user = User::findOrFail($id);
        $this->organizationModel->removeUser($user);

        session()->flash('message', __('User soft deleted successfully.'));
        $this->dispatch('flash-message');
    }

    public function forceDeleteUser($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete();

        session()->flash('message', __('User permanently deleted.'));
        $this->dispatch('flash-message');
    }

    public function restoreUser($id)
    {

        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        session()->flash('message', 'User restored successfully.');
        $this->dispatch('flash-message');
    }
}

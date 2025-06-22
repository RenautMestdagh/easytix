<?php

namespace App\Livewire\Backend\Users;

use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Organization;
use App\Models\User;
use App\Traits\FlashMessage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class EditUser extends Component
{
    use FlashMessage;

    public User $user;

    public $userName = '';
    public $userEmail = '';
    public $userPassword = '';
    public $userPassword_confirmation = '';

    public $role = '';
    public $organization_id = null;

    public $adminCount;

    public $roles = [];
    public $organizations = [];

    public function mount(User $user)
    {
        $this->user = $user;

        $this->userName = $user->name;
        $this->userEmail = $user->email;

        $this->role = $user->roles->first()->name ?? '';
        $this->organization_id = $user->organization_id;

        $this->adminCount = $this->user->organization?->admins()->count() ?? -1;

        $roles = Role::pluck('name', 'name');
        $this->roles = $this->role === 'superadmin'
            ? ['superadmin' => 'superadmin']
            : $roles->except('superadmin')->toArray();

        $this->organizations = Organization::pluck('name', 'id');
    }

    public function updated($propertyName)
    {
        // Validate individual field using UpdateUserRequest
        $fieldRules = (new UpdateUserRequest(
            $this->role,
            $this->organization_id,
            $this->user,
        ))->rules();
        $fieldMessages = (new UpdateUserRequest())->messages();

        // Handle password confirmation case
        if ($propertyName === 'userPassword' || $propertyName === 'userPassword_confirmation') {
            $this->validateOnly('userPassword', $fieldRules, $fieldMessages);
            return;
        }

        if (!empty($this->userEmail) && ($propertyName === 'organization_id')) {
            $this->validateOnly('userEmail', $fieldRules, $fieldMessages);
        }

        if (!array_key_exists($propertyName, $fieldRules)) {
            return; // skip validation if no rule is defined
        }

        $this->validateOnly($propertyName, $fieldRules, $fieldMessages);
    }

    public function update()
    {
        $validatedData = $this->validate(
            (new UpdateUserRequest(
                $this->role,
                $this->organization_id,
                $this->user,
            ))->rules(),
            (new UpdateUserRequest())->messages()
        );

        try {
            $this->user->update(array_filter([
                'organization_id' => $validatedData['organization_id'],
                'name' => $validatedData['userName'],
                'email' => $validatedData['userEmail'] ?? $this->user['email'],
                'password' => !empty($validatedData['userPassword']) ? Hash::make($validatedData['userPassword']) : null
            ]));

            $this->user->syncRoles([$validatedData['role']]);
            $this->flashMessage('User updated successfully.');
            redirect()->route('users.index');
        } catch (\Exception $e) {
            Log::error('An error occurred while updating the user: ' . $e->getMessage());
            $this->flashMessage('An error occurred while updating the user.', 'error');
        }
    }

    public function cancel()
    {
        return redirect()->route('users.index');
    }

    public function render()
    {
        return view('livewire.backend.users.edit-user', [
            'roles' => $this->roles,
            'organizations' => $this->organizations,
            'adminCount' => $this->adminCount,
        ]);
    }
}

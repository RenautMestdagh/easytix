<?php

namespace App\Livewire\Backend\Users;

use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class EditUser extends Component
{
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
            $this->organization_id,
            $this->user->id,
        ))->rules();
        $fieldMessages = (new UpdateUserRequest())->messages();

        // Handle password confirmation case
        if ($propertyName === 'userPassword' || $propertyName === 'userPassword_confirmation') {
            $this->validateOnly('userPassword', $fieldRules, $fieldMessages);
            return;
        }

        if ($propertyName === 'organization_id') {
            $this->validate([
                'userEmail' => $fieldRules['userEmail'],
                'organization_id' => $fieldRules['organization_id'],
            ], $fieldMessages);
            return;
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
                $this->organization_id,
                $this->user->id,
            ))->rules(),
            (new UpdateUserRequest())->messages()
        );

        $validatedData['organization_id'] = session('organization_id') !== null ? session('organization_id') : $validatedData['organization_id'];

        try {
            // Start a database transaction
            return DB::transaction(function () use ($validatedData) {
                // Find the user with lock for update to prevent race conditions
                $user = User::lockForUpdate()->findOrFail($this->user['id']);
                $originalOrganizationId = $user->organization_id;
                $currentRole = $user->roles->first()->name ?? '';
                $isSuperadmin = $currentRole === 'superadmin';
                $isAdmin = $currentRole === 'admin';

                // 1. Prevent superadmin from becoming anything else
                if ($isSuperadmin && $validatedData['role'] !== 'superadmin') {
                    session()->flash('message', __('Superadmin role cannot be changed.'));
                    session()->flash('message_type', 'error');
                    $this->role = 'superadmin';
                    return;
                }

                // 2. Prevent non-superadmins from becoming superadmin
                if (!$isSuperadmin && $validatedData['role'] === 'superadmin') {
                    session()->flash('message', __('Only existing superadmins can assign superadmin role.'));
                    session()->flash('message_type', 'error');
                    $this->role = $currentRole;
                    return;
                }

                // 3. Ensure non-superadmin users have an organization
                if ($validatedData['role'] !== 'superadmin' && empty($validatedData['organization_id'])) {
                    session()->flash('message', __('Non-superadmin users must belong to an organization.'));
                    session()->flash('message_type', 'error');
                    $this->organization_id = $originalOrganizationId;
                    return;
                }

                // 4. Prevent organization assignment for superadmin
                if ($validatedData['role'] === 'superadmin' && !empty($validatedData['organization_id'])) {
                    session()->flash('message', __('Superadmin cannot be assigned to an organization.'));
                    session()->flash('message_type', 'error');
                    $this->organization_id = null;
                    return;
                }

                // Check if we're changing organization
                $isChangingOrganization = $validatedData['organization_id'] != $originalOrganizationId;

                // Check if we're changing from admin role to non-admin
                $isChangingFromAdmin = $isAdmin && $validatedData['role'] !== 'admin';

                // If user is admin and changing organization, check if old org will have at least one admin left
                if ($isAdmin && $isChangingOrganization || $isChangingFromAdmin) {
                    $remainingAdminsInOldOrg = $this->user->organization?->admins()->count();

                    if ($remainingAdminsInOldOrg <= 1) {
                        if($isChangingOrganization)
                            session()->flash('message', __('Cannot change organization. The current organization must have at least one admin.'));
                        else
                            session()->flash('message', __('Cannot change role. There must be at least one admin in the organization.'));
                        session()->flash('message_type', 'error');
                        $this->organization_id = $originalOrganizationId;
                        $this->role = 'admin';
                        return;
                    }
                }

                // Update the user
                $user->update(array_filter([
                    'name' => $validatedData['userName'],
                    'email' => $validatedData['userEmail'] ?? $this->user['email'],
                    'organization_id' => $isSuperadmin ? null : $validatedData['organization_id'],
                    'password' => !empty($validatedData['userPassword']) ? Hash::make($validatedData['userPassword']) : null
                ]));

                // Sync roles (superadmin remains superadmin, others get validated role)
                $user->syncRoles([$validatedData['role']]);

                session()->flash('message', __('User successfully updated.'));
                session()->flash('message_type', 'success');

                return redirect()->route('users.index');
            });
        } catch (\Exception $e) {
            session()->flash('message', __('An error occurred while updating the user'));
            session()->flash('message_type', 'error');
        }
    }

    public function cancel()
    {
        return redirect()->route('users.index');
    }

    public function render()
    {
        return view('livewire.users.edit-user', [
            'roles' => $this->roles,
            'organizations' => $this->organizations,
            'adminCount' => $this->adminCount,
        ]);
    }
}

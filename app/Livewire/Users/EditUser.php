<?php

namespace App\Livewire\Users;

use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class EditUser extends Component
{
    public $userModel;
    public $user;
    public $role = '';
    public $organization_id = null;
    public $adminCount;

    public $roles = [];
    public $organizations = [];

    public function mount(User $user)
    {
        $this->userModel = $user;
        $this->user = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'password' => '',
            'password_confirmation' => '',
        ];
        $this->adminCount = $this->userModel->organization?->admins()->count() ?? -1;

        $this->role = $user->roles->first()->name ?? '';
        $this->organization_id = $user->organization_id;

        $this->roles = Role::all()->pluck('name', 'name')->toArray();
        $this->organizations = Organization::all()->pluck('name', 'id')->toArray();
    }

    public function updated($propertyName)
    {
        $this->resetErrorBag($propertyName);

        // Validate individual field using UpdateUserRequest
        $fieldRules = (new UpdateUserRequest())->rules();
        $fieldMessages = (new UpdateUserRequest())->messages();

        if ($propertyName === 'role' && $this->role === 'superadmin') {
            $this->organization_id = '';
        }

        // Handle password confirmation case
        if ($propertyName === 'user.password_confirmation' || $propertyName === 'user.password') {
            $this->validateOnly('user.password', $fieldRules, $fieldMessages);
            return;
        }

        $value = data_get($this, $propertyName);
        if($this->userModel->email === $value) {
            return;
        }

        if (!array_key_exists($propertyName, $fieldRules)) {
            return; // skip validation if no rule is defined
        }

        $this->validateOnly($propertyName, $fieldRules, $fieldMessages);
    }

    public function update()
    {
        $requestData = $this->prepareRequestData();

        // Get the base rules from UpdateUserRequest
        $rules = (new UpdateUserRequest())->rules();
        $messages = (new UpdateUserRequest())->messages();

        // If email hasn't changed, remove the unique validation
        if ($this->userModel->email === $this->user['email']) {
            unset($rules['user.email']);
        }

        // Validate all fields
        $validatedData = validator($requestData, $rules, $messages)->validate();
        $validatedData['organization_id'] = $validatedData['organization_id'] !== '' ? $validatedData['organization_id'] : null;

        try {
            // Start a database transaction
            return DB::transaction(function () use ($validatedData) {
                // Find the user with lock for update to prevent race conditions
                $user = User::lockForUpdate()->findOrFail($this->user['id']);
                $originalOrganizationId = $user->organization_id;
                $isAdmin = $user->hasRole('admin');
                $isSuperadmin = $user->hasRole('superadmin');

                // Prevent role changes for superadmin
                if ($isSuperadmin && $validatedData['role'] !== 'superadmin') {
                    session()->flash('message', __('Superadmin role cannot be changed.'));
                    session()->flash('message_type', 'error');
                    $this->role = 'superadmin';
                    return;
                }

                // Prevent organization assignment for superadmin
                if ($isSuperadmin && $validatedData['organization_id'] !== null) {
                    session()->flash('message', __('Superadmin cannot be assigned to an organization.'));
                    session()->flash('message_type', 'error');
                    $this->organization_id = null;
                    return;
                }

                // Check if we're changing organization
                $isChangingOrganization = $validatedData['organization_id'] != $originalOrganizationId;

                // Check if we're changing from admin role to non-admin
                $currentRole = $user->roles->first()->name ?? '';
                $isChangingFromAdmin = $currentRole === 'admin' && $validatedData['role'] !== 'admin';

                // If user is admin and changing organization, check if old org will have at least one admin left
                if ($isAdmin && $isChangingOrganization && $originalOrganizationId) {
                    $remainingAdminsInOldOrg = User::role('admin')
                        ->where('organization_id', $originalOrganizationId)
                        ->where('id', '!=', $user->id)
                        ->lockForUpdate()
                        ->count();

                    if ($remainingAdminsInOldOrg < 1) {
                        session()->flash('message', __('Cannot change organization. The current organization must have at least one admin.'));
                        session()->flash('message_type', 'error');
                        $this->organization_id = $originalOrganizationId;
                        return;
                    }
                }

                // If changing from admin role to non-admin, check if org will have at least one admin left
                if ($isChangingFromAdmin && $validatedData['organization_id']) {
                    $remainingAdmins = User::role('admin')
                        ->where('organization_id', $validatedData['organization_id'])
                        ->where('id', '!=', $user->id)
                        ->lockForUpdate()
                        ->count();

                    if ($remainingAdmins < 1) {
                        session()->flash('message', __('Cannot change role. There must be at least one admin in the organization.'));
                        session()->flash('message_type', 'error');
                        $this->role = 'admin';
                        $this->organization_id = $validatedData['organization_id'];
                        return;
                    }
                }

                // Update the user
                $updateData = [
                    'name' => $validatedData['user']['name'],
                    'email' => $validatedData['user']['email'] ?? $this->user['email'],
                    'organization_id' => $isSuperadmin ? null : $validatedData['organization_id'],
                ];

                // Only update password if it was provided
                if (!empty($validatedData['user']['password'])) {
                    $updateData['password'] = Hash::make($validatedData['user']['password']);
                }

                $user->update($updateData);

                // For superadmin, ensure role stays as superadmin
                $roleToSync = $isSuperadmin ? 'superadmin' : $validatedData['role'];
                $user->syncRoles([$roleToSync]);

                session()->flash('message', __('User successfully updated.'));
                session()->flash('message_type', 'success');

                return redirect()->route('users.index');
            });
        } catch (\Exception $e) {
            session()->flash('message', __('An error occurred while updating the user: ' . $e->getMessage()));
            session()->flash('message_type', 'error');
        }
    }

    public function cancel()
    {
        return redirect()->route('users.index');
    }

    protected function prepareRequestData(): array
    {
        return [
            'user' => $this->user,
            'role' => $this->role,
            'organization_id' => $this->organization_id,
        ];
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

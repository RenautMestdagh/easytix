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
    public User $userModel;
    public $user;
    public $role = '';
    public $organization_id = null;
    public $adminCount;

    public $roles = [];
    public $organizations = [];

    public function mount(User $user)
    {
        $this->userModel = $user;
        $this->authorize('users.update', $this->userModel);
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

        // Get all roles initially
        $allRoles = Role::all()->pluck('name', 'name');

        // Filter roles based on current user's role
        if ($this->role === 'superadmin') {
            $this->roles = ['superadmin' => 'superadmin'];
        } else {
            // Remove superadmin role for non-superadmin users
            $this->roles = $allRoles->reject(function ($value, $key) {
                return $value === 'superadmin';
            })->toArray();
        }

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
        $this->authorize('users.update', $this->userModel);
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
                if ($validatedData['role'] === 'superadmin' && $validatedData['organization_id'] !== null) {
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
                        $this->role = 'admin';
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

                // Sync roles (superadmin remains superadmin, others get validated role)
                $user->syncRoles([$validatedData['role']]);

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

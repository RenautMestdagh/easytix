<?php

namespace App\Livewire\Users;

use App\Http\Requests\User\StoreUserRequest;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class CreateUser extends Component
{
    public $user = [
        'name' => '',
        'email' => '',
        'password' => '',
        'password_confirmation' => '',
    ];

    public $userName = '';
    public $userEmail = '';
    public $userPassword = '';
    public $userPassword_confirmation = '';

    public $role = '';
    public $organization_id = null;

    public $roles = []; // Define roles property
    public $organizations = []; // Define organizations property

    public function mount()
    {
        $this->authorize('users.create');

        // Get all roles
        $roles = Role::all()->pluck('name', 'name')->toArray();

        // Remove 'superadmin' if the user doesn't have it
        if (!auth()->user()->hasRole('superadmin')) {
            unset($roles['superadmin']);
        }

        $this->roles = $roles;
        $this->organizations = Organization::all()->pluck('name', 'id')->toArray();
    }

    public function updated($propertyName)
    {
        // Validate individual field using StoreUserRequest
        $fieldRules = (new StoreUserRequest())->rules();
        $fieldMessages = (new StoreUserRequest())->messages();

        if ($propertyName === 'role') {
            $this->resetErrorBag('organization_id');
            if($this->role === 'superadmin')
                $this->organization_id = '';
        }

        // Handle password confirmation case
        if ($propertyName === 'userPassword' || $propertyName === 'userPassword_confirmation') {
            $this->validateOnly('userPassword', $fieldRules, $fieldMessages);
            return;
        }

        if (!array_key_exists($propertyName, $fieldRules)) {
            return; // skip validation if no rule is defined
        }

        $this->validateOnly($propertyName, $fieldRules, $fieldMessages);
    }

    public function save()
    {
        $this->authorize('users.create');

        $validatedData = $this->validate(
            (new StoreUserRequest)->rules(),
            (new StoreUserRequest)->messages()
        );

        // 1. Ensure non-superadmin users have an organization
        if ($validatedData['role'] !== 'superadmin' && empty($validatedData['organization_id'])) {
            session()->flash('message', __('Non-superadmin users must belong to an organization.'));
            session()->flash('message_type', 'error');
            return;
        }

        // 2. Prevent organization assignment for superadmin
        if ($validatedData['role'] === 'superadmin' && !empty($validatedData['organization_id'])) {
            session()->flash('message', __('Superadmin cannot be assigned to an organization.'));
            session()->flash('message_type', 'error');
            $this->organization_id = null;
            return;
        }

        try {
            // Create the user
            $user = User::create([
                'name' => $validatedData['userName'],
                'email' => $validatedData['userEmail'],
                'password' => Hash::make($validatedData['userPassword']),
                'organization_id' => $validatedData['organization_id'],
            ]);

            // Assign the selected role
            $user->assignRole($validatedData['role']);

            session()->flash('message', __('User successfully created.'));
            session()->flash('message_type', 'success');

            return redirect()->route('users.index');

        } catch (\Exception $e) {
            session()->flash('message', __('An error occurred while creating the user: ' . $e->getMessage()));
            session()->flash('message_type', 'error');
        }
    }

    public function render()
    {
        return view('livewire.users.create-user', [
            'roles' => $this->roles,
            'organizations' => $this->organizations,
        ]);
    }
}

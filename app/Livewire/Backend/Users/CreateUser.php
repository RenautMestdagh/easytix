<?php

namespace App\Livewire\Backend\Users;

use App\Http\Requests\User\StoreUserRequest;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Unique;
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
        // Get all roles
        $roles = Role::all()->pluck('name', 'name')->toArray();

        // Remove 'superadmin' if the user doesn't have it
        if (!auth()->user()->hasRole('superadmin')) {
            unset($roles['superadmin']);
        }

        $this->roles = $roles;
        $this->organizations = Organization::all()->pluck('name', 'id')->toArray();
        $this->organization_id = session('organization_id');
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'role') {
            $this->resetErrorBag('organization_id');
            if($this->role === 'superadmin')
                $this->organization_id = null;
            else if($this->organization_id === null)
                $this->organization_id = array_key_first($this->organizations);
        }

        $fieldRules = (new StoreUserRequest($this->organization_id))->rules();
        $fieldMessages = (new StoreUserRequest())->messages();

        // Handle password confirmation case
        if ($propertyName === 'userPassword' || $propertyName === 'userPassword_confirmation') {
            $this->validateOnly('userPassword', $fieldRules, $fieldMessages);
            return;
        }

        if ($propertyName === 'role' || $propertyName === 'organization_id') {
            $this->validate([
                'userEmail' => $fieldRules['userEmail'],
                $propertyName => $fieldRules[$propertyName],
            ], $fieldMessages);
            return;
        }

        if ($propertyName === 'userEmail' && !$this->role) {
            // If no role is selected, dont check on email uniqueness
            $fieldRules = array_filter($fieldRules, function ($rule) {
                return !($rule instanceof Unique);
            });
        }

        if (!array_key_exists($propertyName, $fieldRules)) {
            return; // skip validation if no rule is defined
        }

        $this->validateOnly($propertyName, $fieldRules, $fieldMessages);
    }

    public function save()
    {
        $validatedData = $this->validate(
            (new StoreUserRequest($this->organization_id))->rules(),
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
                'organization_id' => $validatedData['organization_id'],
                'name' => $validatedData['userName'],
                'email' => $validatedData['userEmail'],
                'password' => Hash::make($validatedData['userPassword']),
            ]);

            // Assign the selected role
            $user->assignRole($validatedData['role']);

            session()->flash('message', __('User successfully created.'));
            session()->flash('message_type', 'success');

            return redirect()->route('users.index');

        } catch (\Exception $e) {
            Log::error('An error occurred while creating the user: ' . $e->getMessage());
            session()->flash('message', __('An error occurred while creating the user'));
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

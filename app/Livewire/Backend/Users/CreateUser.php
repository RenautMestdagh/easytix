<?php

namespace App\Livewire\Backend\Users;

use App\Http\Requests\User\StoreUserRequest;
use App\Models\Organization;
use App\Models\User;
use App\Traits\FlashMessage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Unique;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class CreateUser extends Component
{
    use FlashMessage;

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
            if($this->role === 'superadmin')
                $this->organization_id = null;
            else if($this->organization_id === null)
                $this->organization_id = array_key_first($this->organizations);
        }

        $fieldRules = (new StoreUserRequest(
            $this->role,
            $this->organization_id,
        ))->rules();
        $fieldMessages = (new StoreUserRequest())->messages();

        if ($propertyName === 'userEmail' && empty($this->role)) {
            // If no role is selected, dont check on email uniqueness
            $fieldRules['userEmail'] = array_filter($fieldRules['userEmail'], function ($rule) {
                return !($rule instanceof Unique);
            });
        }

        // Handle password confirmation case
        if ($propertyName === 'userPassword' || $propertyName === 'userPassword_confirmation') {
            $this->validateOnly('userPassword', $fieldRules, $fieldMessages);
            return;
        }

        if (!empty($this->userEmail) && ($propertyName === 'role' || $propertyName === 'organization_id')) {
            $this->validateOnly('userEmail', $fieldRules, $fieldMessages);
        }

        if (!array_key_exists($propertyName, $fieldRules)) {
            return; // skip validation if no rule is defined
        }

        $this->validateOnly($propertyName, $fieldRules, $fieldMessages);
    }

    public function save()
    {
        if($this->role === 'superadmin')
            $this->organization_id = null;

        $validatedData = $this->validate(
            (new StoreUserRequest(
                $this->role,
                $this->organization_id,
            ))->rules(),
            (new StoreUserRequest)->messages()
        );

        try {
            // Create the user
            $user = User::create([
                'organization_id' => $validatedData['organization_id'],
                'name' => $validatedData['userName'],
                'email' => $validatedData['userEmail'],
                'password' => Hash::make($validatedData['userPassword']),
            ]);

            $user->assignRole($validatedData['role']);
            $this->flashMessage('User created successfully.');
            redirect()->route('users.index');

        } catch (\Exception $e) {
            Log::error('An error occurred while creating the user: ' . $e->getMessage());
            $this->flashMessage('An error occurred while creating the user.', 'error');
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

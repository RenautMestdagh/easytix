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

    public $role = '';
    public $organization_id = null;

    public $roles = []; // Define roles property
    public $organizations = []; // Define organizations property

    public function mount()
    {
        $this->roles = Role::all()->pluck('name', 'name')->toArray();
        $this->organizations = Organization::all()->pluck('name', 'id')->toArray();
    }

    public function updated($propertyName)
    {
        // Convert Livewire property format to request format
        $requestData = $this->prepareRequestData();

        // Validate individual field using StoreUserRequest
        $fieldRules = (new StoreUserRequest())->rules();
        $fieldMessages = (new StoreUserRequest())->messages();

        if ($propertyName === 'role' && $this->role === 'superadmin') {
            $this->organization_id = '';
        }

        // Handle password confirmation case
        if ($propertyName === 'user.password_confirmation' || $propertyName === 'user.password') {
            $this->validateOnly('user.password', $fieldRules, $fieldMessages);
            return;
        }

        if (!array_key_exists($propertyName, $fieldRules)) {
            return; // skip validation if no rule is defined
        }

        $this->validateOnly($propertyName, $fieldRules, $fieldMessages);
    }

    public function save()
    {
        $requestData = $this->prepareRequestData();

        // Validate all fields using StoreUserRequest
        $validatedData = validator(
            $requestData,
            (new StoreUserRequest())->rules(),
            (new StoreUserRequest())->messages()
        )
            ->validate();
        $validatedData['organization_id'] = $validatedData['organization_id'] !== '' ? $validatedData['organization_id'] : null;


        try {
            // Create the user
            $user = User::create([
                'name' => $validatedData['user']['name'],
                'email' => $validatedData['user']['email'],
                'password' => Hash::make($validatedData['user']['password']),
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
        return view('livewire.users.create-user', [
            'roles' => $this->roles,
            'organizations' => $this->organizations,
        ]);
    }
}

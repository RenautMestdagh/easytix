<?php

namespace App\Livewire\Organizations;

use App\Http\Requests\Organization\StoreOrganizationRequest;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class CreateOrganization extends Component
{
    public $organization = [
        'name' => '',
        'subdomain' => '',
    ];

    public $user = [
        'name' => '',
        'email' => '',
        'password' => '',
        'password_confirmation' => '',
    ];

    /**
     * Lifecycle hook: Called automatically when any property changes.
     * This allows us to validate fields individually as they're updated.
     */

    public function updated($property): void
    {
        $this->resetErrorBag($property);

        $rules = (new StoreOrganizationRequest())->rules();
        $messages = (new StoreOrganizationRequest())->messages();

        // Handle password confirmation case
        if ($property === 'user.password_confirmation' || $property === 'user.password') {
            $this->validateOnly('user.password', $rules, $messages);
            return;
        }

        if (!array_key_exists($property, $rules)) {
            return; // skip validation if no rule is defined
        }

        // Get value
        $value = data_get($this, $property);

        // Convert 'organization.name' to ['organization' => ['name' => 'value']]
        $data = Arr::undot([$property => $value]);

        Validator::make(
            $data,
            [$property => $rules[$property]],
            $messages
        )->validate();
    }


    /**
     * Process form submission
     */
    public function save()
    {
        // Validate all fields using the FormRequest rules
        $this->validate(
            (new StoreOrganizationRequest())->rules(),
            (new StoreOrganizationRequest())->messages()
        );

        try {
            DB::beginTransaction();

            // Create the organization
            $organization = Organization::create([
                'name' => $this->organization['name'],
                'subdomain' => $this->organization['subdomain'],
            ]);

            // Create the user
            $user = User::create([
                'name' => $this->user['name'],
                'email' => $this->user['email'],
                'password' => Hash::make($this->user['password']),
                'organization_id' => $organization->id,
            ]);

            // Assign an administrator role to the user
            $organizerRole = Role::firstOrCreate(['name' => 'admin']);
            $user->assignRole($organizerRole);

            DB::commit();

            session()->flash('message', __('Organisatie succesvol aangemaakt.'));
            session()->flash('message_type', 'success');

            return redirect()->route('organizations.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('message', __('Er is een fout opgetreden bij het aanmaken van de organisatie.'));
            session()->flash('message_type', 'error');
        }
    }

    /**
     * Reset form fields and error messages
     */
    public function resetForm(): void
    {
        $this->reset(['organization', 'user']);
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.organizations.create-organization');
    }
}

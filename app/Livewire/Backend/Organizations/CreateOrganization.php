<?php

namespace App\Livewire\Backend\Organizations;

use App\Http\Requests\Organization\StoreOrganizationRequest;
use App\Models\Organization;
use App\Models\User;
use App\Traits\FlashMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class CreateOrganization extends Component
{
    use FlashMessage;

    public $organizationName = '';
    public$organizationSubdomain = '';


    public $userName = '';
    public $userEmail = '';
    public $userPassword = '';
    public $userPasswordConfirmation = '';

    public function mount()
    {
    }

    /**
     * Lifecycle hook: Called automatically when any property changes.
     * This allows us to validate fields individually as they're updated.
     */

    public function updated($propertyName): void
    {
        $fieldRules = (new StoreOrganizationRequest())->rules();
        $fieldMessages = (new StoreOrganizationRequest())->messages();

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


    /**
     * Process form submission
     */
    public function save()
    {

        // Validate all fields using the FormRequest rules
        $validatedData = $this->validate(
            (new StoreOrganizationRequest())->rules(),
            (new StoreOrganizationRequest())->messages()
        );

        try {
            DB::beginTransaction();

            // Create the organization
            $organization = Organization::create([
                'name' => $validatedData['organizationName'],
                'subdomain' => $validatedData['organizationSubdomain'],
            ]);

            // Create the user
            $user = User::create([
                'name' => $validatedData['userName'],
                'email' => $validatedData['userEmail'],
                'password' => Hash::make($validatedData['userPassword']),
                'organization_id' => $organization->id,
            ]);

            // Assign an administrator role to the user
            $organizerRole = Role::firstOrCreate(['name' => 'admin']);
            $user->assignRole($organizerRole);

            DB::commit();

            $this->flashMessage('Organization successfully created!');
            redirect()->route('organizations.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating organization: ' . $e->getMessage());
            $this->flashMessage('An error occurred while creating the organization.');
        }
    }

    public function render()
    {
        return view('livewire.backend.organizations.create-organization');
    }
}

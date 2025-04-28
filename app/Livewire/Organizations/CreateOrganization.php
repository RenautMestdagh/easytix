<?php

namespace App\Livewire\Organizations;

use App\Http\Requests\Organization\StoreOrganizationRequest;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

    public function save(StoreOrganizationRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Create the organization
            $organization = Organization::create([
                'name' => $validated['organization']['name'],
                'subdomain' => $validated['organization']['subdomain'],
            ]);

            // Create the user
            $user = User::create([
                'name' => $validated['user']['name'],
                'email' => $validated['user']['email'],
                'password' => Hash::make($validated['user']['password']),
                'organization_id' => $organization->id,
            ]);

            // Assign an administrator role to the user
            $organizerRole = Role::firstOrCreate(['name' => 'organizer']);
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

    public function render()
    {
        return view('livewire.organizations.create-organization');
    }
}

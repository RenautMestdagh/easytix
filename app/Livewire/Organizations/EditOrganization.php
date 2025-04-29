<?php

namespace App\Livewire\Organizations;

use App\Http\Requests\Organization\StoreOrganizationRequest;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Models\Organization;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class EditOrganization extends Component
{
    public Organization $organizationModel;
    public $organization = [
        'name' => '',
        'subdomain' => ''
    ];

    public function mount(Organization $organization)
    {
        $this->organizationModel = $organization;
        $this->organization = [
            'name' => $organization->name,
            'subdomain' => $organization->subdomain
        ];
    }

    public function updated($property): void
    {
        $this->resetErrorBag($property);

        $rules = (new StoreOrganizationRequest())->rules();
        $messages = (new StoreOrganizationRequest())->messages();

        if (!array_key_exists($property, $rules)) {
            return; // skip validation if no rule is defined
        }

        // Get value
        $value = data_get($this, $property);

        // Convert 'organization.name' to ['organization' => ['name' => 'value']]
        $data = Arr::undot([$property => $value]);

        if($this->organizationModel->subdomain === $value) {
            return;
        }

        Validator::make(
            $data,
            [$property => $rules[$property]],
            $messages
        )->validate();
    }

    public function save()
    {
//        if (!Auth::user()->can('edit organizations')) {
//            abort(403);
//        }

        // Skip validation for subdomain if it's unchanged
        $rules = (new UpdateOrganizationRequest())->rules();
        $messages = (new UpdateOrganizationRequest())->messages();

        // If the subdomain is not changed, remove its validation rule
        if ($this->organizationModel->subdomain === $this->organization['subdomain']) {
            unset($rules['organization.subdomain']);
        }

        $validated = $this->validate($rules, $messages);

        try {
            $this->organizationModel->update($validated['organization']);

            session()->flash('message', __('Organization successfully updated.'));
            session()->flash('message_type', 'success');

            return redirect()->route('organizations.index');
        } catch (\Exception $e) {
            session()->flash('message', __('An error occurred while updating the organization.'));
            session()->flash('message_type', 'error');
        }
    }

    public function render()
    {
        return view('livewire.organizations.edit-organization');
    }
}

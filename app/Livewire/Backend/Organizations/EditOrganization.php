<?php

namespace App\Livewire\Backend\Organizations;

use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Models\Organization;
use App\Traits\FlashMessage;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class EditOrganization extends Component
{
    use WithPagination, FlashMessage;

    public Organization $organization;
    public $organizationName = '';
    public $organizationSubdomain = '';

    public $saveButtonVisible = false;

    public function mount()
    {
        $this->organization = Organization::findOrFail(session('organization_id'));

        $this->organizationName = $this->organization->name;
        $this->organizationSubdomain = $this->organization->subdomain;
    }

    public function updated($propertyName): void
    {
        $fieldRules = (new UpdateOrganizationRequest($this->organization->id))->rules();
        $fieldMessages = (new UpdateOrganizationRequest())->messages();

        if (!array_key_exists($propertyName, $fieldRules)) {
            return; // skip validation if no rule is defined
        }

        $this->saveButtonVisible = $this->organization->name !== $this->organizationName || $this->organization->subdomain !== $this->organizationSubdomain;

        $this->validateOnly($propertyName, $fieldRules, $fieldMessages);
    }

    public function save()
    {
        $validated = $this->validate(
            (new UpdateOrganizationRequest($this->organization->id))->rules(),
            (new UpdateOrganizationRequest())->messages(),
        );

        try {
            $this->organization->update([
                'name' => $validated['organizationName'],
                'subdomain' => $validated['organizationSubdomain'],
            ]);
            $this->saveButtonVisible = false;
            $this->flashMessage('Organization successfully updated.');

        } catch (\Exception $e) {
            Log::error('Error updating organization: ' . $e->getMessage());
            $this->flashMessage('Error while updating organization.', 'error');
        }

        // If the subdomain has changed, redirect to the new subdomain's edit page
        $oldSubdomain = $this->organization->subdomain;
        $newSubdomain = $validated['organizationSubdomain'];
        if ($oldSubdomain !== $newSubdomain && session('organization_id')) {
            $baseUrl = config('app.url');
            $hostParts = parse_url($baseUrl);

            $scheme = $hostParts['scheme'] ?? 'http';
            $domain = $hostParts['host'] ?? 'localhost';
            $port = isset($hostParts['port']) ? ':' . $hostParts['port'] : '';

            // Construct new host: newsub.localeasytix.org
            $newHost = $newSubdomain . '.' . $domain;

            // Build relative path to edit page
            $path = route('organizations.update', false);

            // Full new URL with port if present
            $newUrl = $scheme . '://' . $newHost . $port . $path;

            redirect()->away($newUrl);
        }
    }


    public function render()
    {
        return view('livewire.backend.organizations.edit-organization');
    }
}

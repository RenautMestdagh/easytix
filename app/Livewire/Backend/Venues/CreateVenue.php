<?php

namespace App\Livewire\Backend\Venues;

use App\Http\Requests\Venue\StoreVenueRequest;
use App\Models\Organization;
use App\Models\Venue;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class CreateVenue extends Component
{
    public $name = '';
    public $latitude = '';
    public $longitude = '';
    public $organization_id = null;

    public $organizations = [];

    public function mount()
    {
        $this->organizations = Organization::all()->pluck('name', 'id')->toArray();
        $this->organization_id = session('organization_id');
    }

    public function updated($propertyName)
    {
        // Validate individual field using StoreVenueRequest
        $fieldRules = (new StoreVenueRequest())->rules();
        $fieldMessages = (new StoreVenueRequest())->messages();

        if (!array_key_exists($propertyName, $fieldRules)) {
            return; // skip validation if no rule is defined
        }

        $this->validateOnly($propertyName, $fieldRules, $fieldMessages);
    }

    public function save()
    {
        $validatedData = $this->validate(
            (new StoreVenueRequest())->rules(),
            (new StoreVenueRequest())->messages()
        );

        try {
            // Create the venue
            $coordinates = $validatedData['latitude'] . ',' . $validatedData['longitude'];

            Venue::create([
                'name' => $validatedData['name'],
                'coordinates' => $coordinates,
                'organization_id' => $validatedData['organization_id'],
            ]);

            session()->flash('message', __('Venue successfully created.'));
            session()->flash('message_type', 'success');

            return redirect()->route('venues.index');

        } catch (\Exception $e) {
            Log::error('An error occurred while creating the venue: ' . $e->getMessage());
            session()->flash('message', __('An error occurred while creating the venue'));
            session()->flash('message_type', 'error');
        }
    }

    public function render()
    {
        return view('livewire.venues.create-venue', [
            'organizations' => $this->organizations,
        ]);
    }
}

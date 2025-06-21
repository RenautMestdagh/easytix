<?php

namespace App\Livewire\Backend\Venues;

use App\Http\Requests\Venue\UpdateVenueRequest;
use App\Models\Organization;
use App\Models\Venue;
use Livewire\Component;

class EditVenue extends Component
{
    public Venue $venue;

    public $name = '';
    public $latitude = '';
    public $longitude = '';
    public $organization_id = null;

    public $organizations = [];

    public function mount(Venue $venue)
    {
        $this->venue = $venue;
        $this->name = $venue->name;

        if ($venue->coordinates) {
            $coords = explode(',', $venue->coordinates);
            $this->latitude = $coords[0] ?? '';
            $this->longitude = $coords[1] ?? '';
        }

        $this->organization_id = $venue->organization_id;
        $this->organizations = Organization::pluck('name', 'id');
    }

    public function updated($propertyName)
    {
        // Validate individual field using UpdateVenueRequest
        $fieldRules = (new UpdateVenueRequest())->rules();
        $fieldMessages = (new UpdateVenueRequest())->messages();

        if (!array_key_exists($propertyName, $fieldRules)) {
            return; // skip validation if no rule is defined
        }

        $this->validateOnly($propertyName, $fieldRules, $fieldMessages);
    }

    public function update()
    {
        $validatedData = $this->validate(
            (new UpdateVenueRequest())->rules(),
            (new UpdateVenueRequest())->messages()
        );

        try {
            $coordinates = $validatedData['latitude'] . ',' . $validatedData['longitude'];

            $this->venue->update([
                'name' => $validatedData['name'],
                'coordinates' => $coordinates,
                'organization_id' => $validatedData['organization_id'] ?? null,
            ]);

            session()->flash('message', __('Venue successfully updated.'));
            session()->flash('message_type', 'success');

            return redirect()->route('venues.index');

        } catch (\Exception $e) {
            session()->flash('message', __('An error occurred while updating the venue'));
            session()->flash('message_type', 'error');
        }
    }

    public function cancel()
    {
        return redirect()->route('venues.index');
    }

    public function render()
    {
        return view('livewire.venues.edit-venue', [
            'organizations' => $this->organizations,
        ]);
    }
}

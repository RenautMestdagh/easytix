<?php

namespace App\Livewire\Backend\Venues;

use App\Http\Requests\Venue\UpdateVenueRequest;
use App\Models\Venue;
use Livewire\Component;

class EditVenue extends Component
{
    public Venue $venue;

    public $name = '';
    public $max_capacity = '';
    public $latitude = '';
    public $longitude = '';

    public function mount(Venue $venue)
    {
        $this->venue = $venue;
        $this->name = $venue->name;
        $this->max_capacity = $venue->max_capacity;

        if ($venue->coordinates) {
            $coords = explode(',', $venue->coordinates);
            $this->latitude = $coords[0] ?? '';
            $this->longitude = $coords[1] ?? '';
        }
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
            $coordinates = (!empty($validatedData['latitude']) && !empty($validatedData['longitude'])) ?
                $validatedData['latitude'] . ',' . $validatedData['longitude'] : null;

            $this->venue->update([
                'name' => $validatedData['name'],
                'max_capacity' => $validatedData['max_capacity'],
                'coordinates' => $coordinates,
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
        return view('livewire.venues.edit-venue');
    }
}

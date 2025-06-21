<?php

namespace App\Livewire\Backend\Venues;

use App\Http\Requests\Venue\StoreVenueRequest;
use App\Models\Venue;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class CreateVenue extends Component
{
    public $name = '';
    public $max_capacity = '';
    public $latitude = '';
    public $longitude = '';

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
            $coordinates = (!empty($validatedData['latitude']) && !empty($validatedData['longitude'])) ?
                $validatedData['latitude'] . ',' . $validatedData['longitude'] : null;

            Venue::create([
                'organization_id' => session('organization_id'),
                'name' => $validatedData['name'],
                'max_capacity' => $validatedData['max_capacity'],
                'coordinates' => $coordinates,
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
        return view('livewire.venues.create-venue');
    }
}

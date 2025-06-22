<?php

namespace App\Livewire\Backend\Venues;

use App\Http\Requests\Venue\StoreVenueRequest;
use App\Models\Venue;
use App\Traits\FlashMessage;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class CreateVenue extends Component
{
    use FlashMessage;

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

        $coordinates = (!empty($validatedData['latitude']) && !empty($validatedData['longitude'])) ?
            $validatedData['latitude'] . ',' . $validatedData['longitude'] : null;

        try {
            Venue::create([
                'organization_id' => session('organization_id'),
                'name' => $validatedData['name'],
                'max_capacity' => $validatedData['max_capacity'],
                'coordinates' => $coordinates,
            ]);
            $this->flashMessage('Venue created successfully.');
            redirect()->route('venues.index');
        } catch (\Exception $e) {
            Log::error('An error occurred while creating the venue: ' . $e->getMessage());
            $this->flashMessage('An error occurred while creating the venue.', 'error');
        }
    }

    public function render()
    {
        return view('livewire.venues.create-venue');
    }
}

<?php

namespace App\Livewire\Backend\Events;

use App\Http\Requests\Event\StoreEventRequest;
use App\Models\Event;
use App\Models\Venue;
use App\Traits\EventManagementUtilities;
use App\Traits\FlashMessage;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateEvent extends Component
{
    use WithFileUploads, EventManagementUtilities, FlashMessage;

    // Event fields
    public $name = '';
    public $description = '';
    public $venue_id = '';
    public $date = '';
    public $max_capacity = null;

    public $event_image;
    public $header_image;
    public $background_image;

    public $is_published = false;
    public $publish_at = null;
    public $publish_option = 'publish_now';

    protected $listeners = ['venueSelected'];

    public function updated($propertyName): void
    {
        if($propertyName === 'publish_option') {
            $this->resetErrorBag('publish_at');
            if($this->publish_option !== 'schedule')
                $this->publish_at = null;
        }

        $fieldRules = (new StoreEventRequest(
            $this->publish_option,
            $this->date,
        ))->rules();
        $fieldMessages = (new StoreEventRequest())->messages();

        if($propertyName === 'event_image' || $propertyName === 'background_image' || $propertyName === 'header_image') {
            try {
                $this->validateOnly($propertyName, $fieldRules, $fieldMessages);
            } catch (Exception $e) {
                $this->$propertyName = null;
                $this->setErrorBag([$propertyName => $e->validator->getMessageBag()->toArray()[$propertyName][0]]);
            }
            return;
        }

        if (!array_key_exists($propertyName, $fieldRules)) {
            return; // skip validation if no rule is defined
        }

        $this->validateOnly($propertyName, $fieldRules, $fieldMessages);
    }

    public function venueSelected($venueId, $venueName)
    {
        $this->venue_id = $venueId;
        $venue = Venue::find($venueId);
        if(empty($this->max_capacity) && $venue?->max_capacity)
            $this->max_capacity = $venue->max_capacity;
    }

    public function store()
    {
        if($this->publish_option !== 'schedule')
            $this->publish_at = null;

        // Validate all fields
        $validatedData = $this->validate(
            (new StoreEventRequest(
                $this->publish_option,
                $this->date,
            ))->rules(),
            (new StoreEventRequest())->messages(),
        );

        $publishStatus = $this->determinePublishStatus();
        $event = null;
        try {
            while (true) {
                // In try catch because uniqid may not be unique but must be
                try {
                    $event = Event::create([
                        'organization_id' => Auth::user()->organization_id,
                        'uniqid' => str_replace('-', '', Str::uuid()),
                        'name' => $validatedData['name'],
                        'description' => $validatedData['description'],
                        'venue_id' => $validatedData['venue_id'],
                        'date' => $validatedData['date'],
                        'max_capacity' => $validatedData['max_capacity'],
                        'is_published' => $publishStatus['is_published'],
                        'publish_at' => $publishStatus['publish_at'],
                    ]);
                    break;
                } catch (QueryException $e) {}
            }

            $this->flashMessage('Event created successfully.');

            // Handle file uploads
            try{
                if ($this->event_image)
                    $this->uploadImage($event, 'event_image');
                if ($this->header_image)
                    $this->uploadImage($event, 'header_image');
                if ($this->background_image)
                    $this->uploadImage($event, 'background_image');
            } catch (Exception $e) {
                Log::error('An error occurred while uploading images: ' . $e->getMessage());
                $this->flashMessage('Error while uploading images.', 'error');
            }

            redirect()->route('ticket-types.index', $event);
        } catch (Exception $e) {
            Log::error('An error occurred while creating the event: ' . $e->getMessage());
            $this->flashMessage('Error while creating event.', 'error');
        }
    }

    public function render()
    {
        return view('livewire.backend.events.create-event', [
            'venues' => Venue::orderBy('name')->limit(10)->get(),
        ]);
    }

    public function cancel()
    {
        return redirect()->route('events.index');
    }
}

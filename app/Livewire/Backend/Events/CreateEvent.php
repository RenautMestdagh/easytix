<?php

namespace App\Livewire\Backend\Events;

use App\Http\Requests\Event\StoreEventRequest;
use App\Models\Event;
use App\Models\Organization;
use App\Models\Venue;
use App\Traits\EventManagementUtilities;
use App\Traits\FlashMessage;
use Exception;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateEvent extends Component
{
    use WithFileUploads, EventManagementUtilities, FlashMessage;

    public $orgSubdomain;

    // Event fields
    public $name = '';
    public $description = '';
    public $subdomain = null;
    public $venue_id = null;
    public $use_venue_capacity = false;
    public $date = '';
    public $max_capacity = null;

    public $event_image;
    public $header_image;
    public $background_image;
    public $event_imageInput;
    public $header_imageInput;
    public $background_imageInput;

    public $is_published = false;
    public $publish_at = null;
    public $publish_option = 'publish_now';

    protected $listeners = ['venueSelected'];

    public function mount()
    {
        $this->orgSubdomain = Organization::first()->subdomain;
    }

    public function updated($propertyName): void
    {
        // Only check images on submit. They are already checked by dropzone
        if (str_ends_with($propertyName, 'Input')) {
            return;
        }

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

        if (!array_key_exists($propertyName, $fieldRules)) {
            return; // skip validation if no rule is defined
        }

        $this->validateOnly($propertyName, $fieldRules, $fieldMessages);
    }

    public function store()
    {
        if($this->publish_option !== 'schedule')
            $this->publish_at = null;

        $this->event_image = $this->event_imageInput ? new File($this->event_imageInput[0]['path']) : null;
        $this->header_image = $this->header_imageInput ? new File($this->header_imageInput[0]['path']) : null;
        $this->background_image = $this->background_imageInput ? new File($this->background_imageInput[0]['path']) : null;

        // Validate all fields
        $validatedData = $this->validate(
            (new StoreEventRequest(
                $this->publish_option,
                $this->date,
            ))->rules(),
            (new StoreEventRequest())->messages(),
        );

        $publishStatus = $this->determinePublishStatus();
        try {
            $event = Event::create([
                'organization_id' => Auth::user()->organization_id,
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'subdomain' => $validatedData['subdomain'],
                'venue_id' => $validatedData['venue_id'],
                'use_venue_capacity' => $validatedData['use_venue_capacity'],
                'date' => $validatedData['date'],
                'max_capacity' => $validatedData['max_capacity'],
                'is_published' => $publishStatus['is_published'],
                'publish_at' => $publishStatus['publish_at'],
            ]);

            $this->flashMessage('Event created successfully.');

            // Handle file uploads
            try{
                foreach (['event_image', 'header_image', 'background_image'] as $mediaType) {
                    $inputField = $mediaType . 'Input';

                    if ($this->$inputField) {
                        $event->$mediaType = $this->saveMedia($mediaType, $event->id);
                        $this->$inputField = null;
                        $this->$mediaType = null;
                    }
                }
                $event->save();
            } catch (Exception $e) {
                Log::error('An error occurred while uploading images: ' . $e->getMessage());
                $this->flashMessage('Error while uploading images.', 'error');

                $event->event_image = null;
                $event->header_image = null;
                $event->background_image = null;
                Storage::disk('public')->deleteDirectory("events/$event->id");
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

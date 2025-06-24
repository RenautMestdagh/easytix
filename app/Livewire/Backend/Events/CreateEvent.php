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
use League\Flysystem\UnableToMoveFile;
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

    public $event_imageInput;
    public $header_imageInput;
    public $background_imageInput;
    public $event_image_validation;
    public $header_image_validation;
    public $background_image_validation;

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

        if($propertyName === 'event_imageInput' || $propertyName === 'header_imageInput' || $propertyName === 'background_imageInput') {
            $validationField = str_replace('Input', '_validation', $propertyName);
            $this->{$validationField} = new File($this->{$propertyName}[0]['path']);

            try {
                $this->validateOnly($validationField, $fieldRules, $fieldMessages);
            } catch (Exception $e) {
                $this->$propertyName = null;
                $this->setErrorBag([$validationField => $e->validator->getMessageBag()->toArray()[$validationField][0]]);
            }
            $this->{$validationField} = null;
            return;
        }

        if ($propertyName === 'date' || $propertyName === 'publish_at') {
            if($this->date) $this->validateOnly('date', $fieldRules, $fieldMessages);
            if($this->publish_at) $this->validateOnly('publish_at', $fieldRules, $fieldMessages);
            return;
        }

        if (!array_key_exists($propertyName, $fieldRules)) {
            return; // skip validation if no rule is defined
        }

        $this->validateOnly($propertyName, $fieldRules, $fieldMessages);
    }

    public function store()
    {
        if($this->publish_option !== 'schedule')
            $this->publish_at = null;

        $this->event_imageInput = $this->event_imageInput[0] ?? null;
        $this->header_imageInput = $this->header_imageInput[0] ?? null;
        $this->background_imageInput = $this->background_imageInput[0] ?? null;
        $this->event_image_validation = $this->event_imageInput ? new File($this->event_imageInput['path']) : null;
        $this->header_image_validation = $this->header_imageInput ? new File($this->header_imageInput['path']) : null;
        $this->background_image_validation = $this->background_imageInput ? new File($this->background_imageInput['path']) : null;

        // Validate all fields
        $validatedData = $this->validate(
            (new StoreEventRequest(
                $this->publish_option,
                $this->date,
            ))->rules(),
            (new StoreEventRequest())->messages(),
        );

        // Necessary because Laravel throws error otherwise. "Property type not supported in Livewire for property: [{}]"
        $this->event_image_validation = null;
        $this->header_image_validation = null;
        $this->background_image_validation = null;

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

                    if($this->{$inputField}) {
                        $fileName = $this->saveMedia($this->{$inputField}, $event->id);
                        if(!$fileName)
                            throw new UnableToMoveFile('Unable to save uploaded file.');
                        $event->{$mediaType} = $fileName;
                    }
                }
            } catch (Exception $e) {
                Log::error('An error occurred while uploading images: ' . $e->getMessage());
                $this->flashMessage('Error while uploading images.', 'error');

                $event->event_image = null;
                $event->header_image = null;
                $event->background_image = null;
                Storage::disk('public')->deleteDirectory("events/$event->id");
            }

            $event->save();
            redirect()->route('ticket-types.index', $event);
        } catch (Exception $e) {
            $this->event_imageInput = [$this->event_imageInput];
            $this->header_imageInput = [$this->header_imageInput];
            $this->background_imageInput = [$this->background_imageInput];
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

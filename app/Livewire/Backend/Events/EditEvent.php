<?php

namespace App\Livewire\Backend\Events;

use App\Http\Requests\Event\UpdateEventRequest;
use App\Models\Event;
use App\Models\Venue;
use App\Traits\EventManagementUtilities;
use App\Traits\FlashMessage;
use Exception;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditEvent extends Component
{
    use WithFileUploads, EventManagementUtilities, FlashMessage;

    public Event $event;

    // Event fields
    public $name;
    public $description;
    public $subdomain = null;
    public $venue_id;
    public $use_venue_capacity = false;
    public $date;
    public ?int $max_capacity;

    public $event_image;
    public $header_image;
    public $background_image;
    public $event_imageInput;
    public $header_imageInput;
    public $background_imageInput;

    public $is_published;
    public $publish_at;
    public $publish_option;

    protected $listeners = [
        'venueSelected',
        'fileRemoved' => 'handleFileRemoval'
    ];

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->name = $event->name;
        $this->description = $event->description;
        $this->subdomain = $event->subdomain;
        $this->venue_id = $event->venue_id;
        $this->use_venue_capacity = $event->use_venue_capacity;
        $this->date = $event->date->format('Y-m-d\TH:i');
        $this->max_capacity = $event->max_capacity;
        $this->is_published = $event->is_published;
        $this->publish_at = $event->publish_at ? $event->publish_at->format('Y-m-d\TH:i') : null;

        $this->event_imageInput = $this->getFileInputData('event_image');
        $this->header_imageInput = $this->getFileInputData('header_image');
        $this->background_imageInput = $this->getFileInputData('background_image');

        // Set publish option based on current status
        if ($event->is_published) {
            $this->publish_option = 'publish_now';
        } elseif ($event->publish_at) {
            $this->publish_option = 'schedule';
        } else {
            $this->publish_option = 'unlisted';
        }
    }

    protected function getFileInputData($attribute)
    {
        if (empty($this->event->{$attribute})) {
            return [];
        } else if (!Storage::disk('public')->exists("events/{$this->event->id}/{$this->event->{$attribute}}")) {
            $this->event->update([$attribute => null]);
            return [];
        }

        $storagePath = "events/{$this->event->id}/{$this->event->{$attribute}}";

        return [
            [
                'path' => Storage::disk('public')->path($storagePath),
                'size' => Storage::disk('public')->size($storagePath),
                'extension' => pathinfo($this->event->{$attribute}, PATHINFO_EXTENSION),
                'type' => Storage::disk('public')->mimeType($storagePath),
                'tmpFilename' => Storage::disk('public')->path($storagePath),
                'temporaryUrl'  => Storage::disk('public')->url($storagePath),
                'dbField' => $attribute,
            ]
        ];
    }

    public function updated($propertyName): void
    {
        if($propertyName === 'publish_option') {
            $this->resetErrorBag('publish_at');
            if($this->publish_option !== 'schedule')
                $this->publish_at = null;
        }

        $fieldRules = (new UpdateEventRequest(
            $this->publish_option,
            $this->date,
            $this->event,
        ))->rules();
        $fieldMessages = (new UpdateEventRequest())->messages();

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

    public function update()
    {
        if($this->publish_option !== 'schedule')
            $this->publish_at = null;

        $old_event_image = $this->event->event_image;
        $old_header_image = $this->event->header_image;
        $old_background_image = $this->event->background_image;
        $this->event_image = $this->event_imageInput ? new File($this->event_imageInput[0]['path']) : null;
        $this->header_image = $this->header_imageInput ? new File($this->header_imageInput[0]['path']) : null;
        $this->background_image = $this->background_imageInput ? new File($this->background_imageInput[0]['path']) : null;

        // Validate all fields
        $validatedData = $this->validate(
            (new UpdateEventRequest(
                $this->publish_option,
                $this->date,
                $this->event,
            ))->rules(),
            (new UpdateEventRequest())->messages()
        );

        try {
            // Determine publish status
            $publishStatus = $this->determinePublishStatus();

            // Update the event
            $this->event->update([
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
            $this->flashMessage('Event updated successfully.');

            try{
                foreach (['event_image', 'header_image', 'background_image'] as $mediaType) {
                    $inputField = $mediaType . 'Input';
                    $oldField = "old_{$mediaType}";
                    if($$oldField) {
                        $deleted = $this->removeUpload($this->event, $mediaType);
                        if($deleted)
                            $this->event->$mediaType = null;
                    }
                    if ($this->$inputField) {
                        $this->event->$mediaType = $this->saveMedia($mediaType, $this->event->id);
                        $this->$inputField = null;
                        $this->$mediaType = null;
                    }
                }
                $this->event->save();
            } catch (Exception $e) {
                Log::error('An error occurred while uploading images: ' . $e->getMessage());
                $this->flashMessage('Error while uploading images.', 'error');

                $this->event->event_image = null;
                $this->event->header_image = null;
                $this->event->background_image = null;
                Storage::disk('public')->deleteDirectory("events/$this->event->id");
            }

            redirect(session()->pull('events.edit.referrer', route('events.index')));
        } catch (\Exception $e) {
            Log::error('An error occurred while updating the event: ' . $e->getMessage());
            $this->flashMessage('An error occurred while updating the event.', 'error');
        }

        if($publishStatus['is_published']) {
            // Publish any ticket types that should publish with the event
            try {
                $ticketTypesToPublish = $this->event->ticketTypes()
                    ->where('publish_with_event', true)
                    ->where('is_published', false)
                    ->get();

                foreach ($ticketTypesToPublish as $ticketType) {
                    $ticketType->update([
                        'is_published' => true,
                        'publish_at' => null // Clear the publish_at since it's now published
                    ]);
                }

            } catch (\Exception $e) {
                Log::error('An error occurred while publishing ticket types: ' . $e->getMessage());
                $this->flashMessage('An error occurred while publishing ticket types.', 'error');
            }
        }
    }

    public function render()
    {
        return view('livewire.backend.events.edit-event', [
            'venues' => Venue::orderBy('name')->limit(10)->get(),
        ]);
    }

    public function cancel()
    {
        return redirect(session()->pull('events.edit.referrer', route('events.index')));
    }
}

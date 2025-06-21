<?php

namespace App\Livewire\Backend\Events;

use App\Http\Requests\Event\UpdateEventRequest;
use App\Models\Event;
use App\Traits\EventManagementUtilities;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditEvent extends Component
{
    use WithFileUploads, EventManagementUtilities;

    public Event $event;

    // Event fields
    public $name;
    public $description;
    public $venue_id;
    public $date;
    public ?int $max_capacity;

    public $event_image;
    public $header_image;
    public $background_image;

    public $is_published;
    public $publish_at;
    public $publish_option;

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->name = $event->name;
        $this->description = $event->description;
        $this->venue_id = $event->venue_id;
        $this->date = $event->date->format('Y-m-d\TH:i');
        $this->max_capacity = $event->max_capacity;
        $this->is_published = $event->is_published;
        $this->publish_at = $event->publish_at ? $event->publish_at->format('Y-m-d\TH:i') : null;

        // Set publish option based on current status
        if ($event->is_published) {
            $this->publish_option = 'publish_now';
        } elseif ($event->publish_at) {
            $this->publish_option = 'schedule';
        } else {
            $this->publish_option = 'unlisted';
        }
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

        if (!array_key_exists($propertyName, $fieldRules)) {
            return; // skip validation if no rule is defined
        }

        $this->validateOnly($propertyName, $fieldRules, $fieldMessages);
    }

    public function update()
    {
        if($this->publish_option !== 'schedule')
            $this->publish_at = null;

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
                'venue_id' => $validatedData['venue_id'],
                'date' => $validatedData['date'],
                'max_capacity' => $validatedData['max_capacity'],
                'is_published' => $publishStatus['is_published'],
                'publish_at' => $publishStatus['publish_at'],
            ]);

            if($publishStatus['is_published']) {
                // Publish any ticket types that should publish with the event
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
            }

            // Handle file uploads
            if ($this->event_image) {
                $this->uploadImage($this->event, 'event_image');
            }

            if ($this->header_image) {
                $this->uploadImage($this->event, 'header_image');
            }

            if ($this->background_image) {
                $this->uploadImage($this->event, 'background_image');
            }

            session()->flash('message', __('Event successfully updated.'));
            session()->flash('message_type', 'success');

            return redirect(session()->pull('events.edit.referrer', route('events.index')));
        } catch (\Exception $e) {
            Log::error($e);
            session()->flash('message', __('An error occurred while updating the event.'));
            session()->flash('message_type', 'error');
        }
    }

    public function render()
    {
        return view('livewire.events.edit-event');
    }

    public function cancel()
    {
        return redirect(session()->pull('events.edit.referrer', route('events.index')));
    }
}

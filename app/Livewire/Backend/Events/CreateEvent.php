<?php

namespace App\Livewire\Backend\Events;

use App\Http\Requests\Event\StoreEventRequest;
use App\Models\Event;
use App\Traits\EventManagementUtilities;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateEvent extends Component
{
    use WithFileUploads, EventManagementUtilities;

    // Event fields
    public $name = '';
    public $description = '';
//    public $location = '';
    public $date = '';
    public $max_capacity = null;

    public $event_image;
    public $header_image;
    public $background_image;

    public $is_published = false;
    public $publish_at = null;
    public $publish_option = 'publish_now';

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

        if (!array_key_exists($propertyName, $fieldRules)) {
            return; // skip validation if no rule is defined
        }

        $this->validateOnly($propertyName, $fieldRules, $fieldMessages);
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

        try {
            // Determine publish status
            $publishStatus = $this->determinePublishStatus();

            // Create the event
            $event = null;
            while (true) {
                try {
                    $event = Event::create([
                        'uniqid' => str_replace('-', '', Str::uuid()),
                        'organization_id' => Auth::user()->organization_id,
                        'name' => $validatedData['name'],
                        'description' => $validatedData['description'],
//                        'location' => $validatedData['location'], TODO
                        'date' => $validatedData['date'],
                        'max_capacity' => $validatedData['max_capacity'],
                        'is_published' => $publishStatus['is_published'],
                        'publish_at' => $publishStatus['publish_at'],
                    ]);
                    break;
                } catch (QueryException $e) {}
            }

            // Handle file uploads
            if ($this->event_image) {
                $this->uploadImage($event, 'event_image');
            }

            if ($this->header_image) {
                $this->uploadImage($event, 'header_image');
            }

            if ($this->background_image) {
                $this->uploadImage($event, 'background_image');
            }

            session()->flash('message', __('Event successfully created.'));
            session()->flash('message_type', 'success');

            return redirect()->route('ticket-types.index', $event);

        } catch (Exception $e) {
            Log::error($e);
            session()->flash('message', __('An error occurred while creating the event.'));
            session()->flash('message_type', 'error');
        }
    }

    public function render()
    {
        return view('livewire.events.create-event');
    }

    public function cancel()
    {
        return redirect()->route('events.index');
    }
}

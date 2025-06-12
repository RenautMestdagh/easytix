<?php

namespace App\Livewire\Events;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class EditEvent extends Component
{
    use WithFileUploads;

    public Event $event;

    // Event fields
    public $name;
    public $description;
    public $location;
    public $date;
    public $max_capacity;

    public $event_image;
    public $background_image;

    public $is_published;
    public $publish_at;
    public $publish_option;

    // Hardcoded variable to prevent unpublishing
    public $preventUnpublish = false;

    public function mount(Event $event)
    {
        $this->authorize('events.update', $event);

        $this->event = $event;
        $this->name = $event->name;
        $this->description = $event->description;
        $this->location = $event->location;
        $this->date = $event->date->format('Y-m-d\TH:i');
        $this->max_capacity = $event->max_capacity;
        $this->is_published = $event->is_published;
        $this->publish_at = $event->publish_at ? $event->publish_at->format('Y-m-d\TH:i') : null;

        $this->preventUnpublish = $event->tickets()->count() > 0;

        // Set publish option based on current status
        if ($event->is_published) {
            $this->publish_option = 'publish_now';
        } elseif ($event->publish_at) {
            $this->publish_option = 'schedule';
        } else {
            $this->publish_option = 'draft';
        }
    }

    public function updated($propertyName): void
    {
        $this->resetErrorBag($propertyName);

        // Handle file upload validation separately
        if (in_array($propertyName, ['event_image', 'background_image'])) {
            $this->validateOnly($propertyName, $this->fileRules());
            return;
        }

        // Handle other field validation
        $this->validateOnly($propertyName, $this->rules());
    }

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'string',
            'location' => 'required|string|max:255',
            'date' => 'required|date|after:now',
            'max_capacity' => 'required|integer|min:1',
            'publish_option' => 'required|in:publish_now,schedule,draft',
            'publish_at' => [
                'nullable',
                'required_if:publish_option,schedule',
                'date',
                'after:now',
                function ($attribute, $value, $fail) {
                    if ($this->publish_option === 'schedule' && $value >= $this->date) {
                        $fail(__('The publish date must be before the event date.'));
                    }
                },
            ],
        ];

        // If event is published and preventUnpublish is true, restrict to only published options
        if ($this->event->is_published && $this->preventUnpublish) {
            $rules['publish_option'] = 'required|in:publish_now';
        }

        return $rules;
    }

    protected function fileRules()
    {
        return [
            'event_image' => 'nullable|image|max:2048', // 2MB
            'background_image' => 'nullable|image|max:5120', // 5MB
        ];
    }

    public function update()
    {
        $this->authorize('events.update', $this->event);

        // Validate all fields
        $validatedData = $this->validate(array_merge(
            $this->rules(),
            $this->fileRules()
        ));

        try {
            // Determine publish status
            $publishStatus = $this->determinePublishStatus();

            // Update the event
            $this->event->update([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'location' => $validatedData['location'],
                'date' => $validatedData['date'],
                'max_capacity' => $validatedData['max_capacity'],
                'is_published' => $publishStatus['is_published'],
                'publish_at' => $publishStatus['publish_at'],
            ]);

            // Handle file uploads
            if ($this->event_image) {
                $this->uploadEventImage();
            }

            if ($this->background_image) {
                $this->uploadBackgroundImage();
            }

            session()->flash('message', __('Event successfully updated.'));
            session()->flash('message_type', 'success');

            return redirect()->route('events.index');

        } catch (\Exception $e) {
            Log::error($e);
            session()->flash('message', __('An error occurred while updating the event.'));
            session()->flash('message_type', 'error');
        }
    }

    protected function determinePublishStatus()
    {
        // If preventUnpublish is true and event is published, ensure it stays published
        if ($this->preventUnpublish && $this->event->is_published && $this->publish_option === 'draft') {
            $this->publish_option = 'publish_now'; // Force publish_now
        }

        return match ($this->publish_option) {
            'publish_now' => ['is_published' => true, 'publish_at' => null],
            'schedule' => ['is_published' => false, 'publish_at' => $this->publish_at],
            'draft' => ['is_published' => false, 'publish_at' => null],
        };
    }

    protected function uploadEventImage()
    {
        // Delete old image if exists
        if ($this->event->event_image) {
            Storage::disk('public')->delete("events/{$this->event->id}/{$this->event->event_image}");
        }

        $filename = $this->generateUniqueFilename('event', $this->event_image->extension(), $this->event->id);

        $this->event_image->storeAs(
            "events/{$this->event->id}",
            $filename,
            'public'
        );

        $this->event->update(['event_image' => $filename]);
    }

    protected function uploadBackgroundImage()
    {
        // Delete old image if exists
        if ($this->event->background_image) {
            Storage::disk('public')->delete("events/{$this->event->id}/{$this->event->background_image}");
        }

        $filename = $this->generateUniqueFilename('background', $this->background_image->extension(), $this->event->id);

        $this->background_image->storeAs(
            "events/{$this->event->id}",
            $filename,
            'public'
        );

        $this->event->update(['background_image' => $filename]);
    }

    protected function generateUniqueFilename($prefix, $extension, $eventId)
    {
        $filename = "{$prefix}.{$extension}";

        // If file exists, append a random string
        if (Storage::disk('public')->exists("events/{$eventId}/{$filename}")) {
            $random = Str::random(8);
            $filename = "{$prefix}_{$random}.{$extension}";
        }

        return $filename;
    }

    public function removeEventImage()
    {
        // Delete the file from storage if it exists
        if ($this->event->event_image) {
            Storage::disk('public')->delete("events/{$this->event->id}/{$this->event->event_image}");
        }

        // Update the event to remove the image reference
        $this->event->update(['event_image' => null]);

        // Reset the file input
        $this->reset('event_image');

        // Show success message
        session()->flash('message', __('Event image removed successfully.'));
        session()->flash('message_type', 'success');
    }

    public function removeBackgroundImage()
    {
        // Delete the file from storage if it exists
        if ($this->event->background_image) {
            Storage::disk('public')->delete("events/{$this->event->id}/{$this->event->background_image}");
        }

        // Update the event to remove the image reference
        $this->event->update(['background_image' => null]);

        // Reset the file input
        $this->reset('background_image');

        // Show success message
        session()->flash('message', __('Background image removed successfully.'));
        session()->flash('message_type', 'success');
    }

    public function render()
    {
        return view('livewire.events.edit-event');
    }

    public function cancel()
    {
        return redirect()->route('events.index');
    }
}

<?php

namespace App\Livewire\Backend\Events;

use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateEvent extends Component
{
    use WithFileUploads;

    public Event $event;

    // Event fields
    public $name = '';
    public $description = '';
    public $location = '';
    public $date = '';
    public $max_capacity = '';

    public $event_image;
    public $header_image;
    public $background_image;

    public $is_published = false;
    public $publish_at = null;
    public $publish_option = 'publish_now';

    public function mount()
    {
        $this->authorize('events.create');
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
        return [
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
    }

    protected function fileRules()
    {
        return [
            'event_image' => 'nullable|image|max:2048', // 2MB
            'header_image' => 'nullable|image|max:2048', // 2MB
            'background_image' => 'nullable|image|max:5120', // 5MB
        ];
    }

    public function store()
    {
        $this->authorize('events.create');

        // Validate all fields
        $validatedData = $this->validate(array_merge(
            $this->rules(),
            $this->fileRules()
        ));

        try {
            // Determine publish status
            $publishStatus = $this->determinePublishStatus();

            // Create the event
            $event = Event::create([
                'uniqid' => str_replace('-', '', Str::uuid()),
                'organization_id' => Auth::user()->organization_id,
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'location' => $validatedData['location'],
                'date' => $validatedData['date'],
                'max_capacity' => $validatedData['max_capacity'],
                'is_published' => $publishStatus['is_published'],
                'publish_at' => $publishStatus['publish_at'],
                'user_id' => Auth::id(),
            ]);

            // Handle file uploads
            if ($this->event_image) {
                $this->uploadEventImage($event);
            }

            if ($this->header_image) {
                $this->uploadHeaderImage($event);
            }

            if ($this->background_image) {
                $this->uploadBackgroundImage($event);
            }

            session()->flash('message', __('Event successfully created.'));
            session()->flash('message_type', 'success');

            return redirect()->route('tickettypes.show', $event);

        } catch (\Exception $e) {
            Log::error($e);
            session()->flash('message', __('An error occurred while creating the event.'));
            session()->flash('message_type', 'error');
        }
    }

    protected function determinePublishStatus()
    {
        return match ($this->publish_option) {
            'publish_now' => ['is_published' => true, 'publish_at' => null],
            'schedule' => ['is_published' => false, 'publish_at' => $this->publish_at],
            'draft' => ['is_published' => false, 'publish_at' => null],
        };
    }

    protected function uploadEventImage($event)
    {
        $filename = $this->generateUniqueFilename('event', $this->event_image->extension(), $event->id);

        $this->event_image->storeAs(
            "events/{$event->id}",
            $filename,
            'public'
        );

        $event->update(['event_image' => $filename]);
    }

    protected function uploadHeaderImage($event)
    {
        $filename = $this->generateUniqueFilename('header', $this->event_image->extension(), $event->id);

        $this->event_image->storeAs(
            "events/{$event->id}",
            $filename,
            'public'
        );

        $event->update(['header_image' => $filename]);
    }

    protected function uploadBackgroundImage($event)
    {
        $filename = $this->generateUniqueFilename('background', $this->background_image->extension(), $event->id);

        $this->background_image->storeAs(
            "events/{$event->id}",
            $filename,
            'public'
        );

        $event->update(['background_image' => $filename]);
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

    public function render()
    {
        return view('livewire.events.create-event');
    }

    public function cancel()
    {
        return redirect()->route('events.index');
    }
}

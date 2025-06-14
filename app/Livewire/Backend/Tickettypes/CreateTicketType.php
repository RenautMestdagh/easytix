<?php

namespace App\Livewire\Backend\Tickettypes;

use App\Models\Event;
use App\Models\TicketType;
use Livewire\Component;

class CreateTicketType extends Component
{
    public Event $event;
    public string $name = '';
    public string $price_euros = ''; // Changed from price_cents to price_euros
    public ?int $available_quantity = null;
    public bool $is_published = false;
    public ?string $publish_at = null;
    public string $publish_option = 'publish_now';
    public bool $publish_with_event = false;

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->authorize('tickets.create');
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'price_euros' => [
                'required',
                function ($attribute, $value, $fail) {
                    // Replace comma with dot for consistent decimal handling
                    $normalizedValue = str_replace(',', '.', $value);

                    if (!is_numeric($normalizedValue)) {
                        $fail(__('The price must be a valid number.'));
                        return;
                    }

                    if ($normalizedValue < 0) {
                        $fail(__('The price must be at least 0.'));
                    }
                },
            ],
            'available_quantity' => 'nullable|integer|min:1',
            'publish_option' => 'required|in:publish_now,schedule,draft,with_event',
            'publish_at' => [
                'nullable',
                'required_if:publish_option,schedule',
                'date',
                'after:now',
                function ($attribute, $value, $fail) {
                    if ($this->publish_option === 'schedule') {
                        $publishAt = \Illuminate\Support\Carbon::parse($value);
                        $eventDate = $this->event->date; // already cast via model

                        if ($publishAt->greaterThanOrEqualTo($eventDate)) {
                            $fail(__('The publish date must be before the event date.'));
                        }
                    }
                },
            ],
            'publish_with_event' => 'boolean',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function store()
    {
        $this->authorize('tickets.create');
        $validatedData = $this->validate();

        $publishStatus = $this->determinePublishStatus();

        // Convert euros to cents for database storage
        $priceInCents = (int) round(str_replace(',', '.', $validatedData['price_euros']) * 100);

        $ticketType = new TicketType([
            'event_id' => $this->event->id,
            'name' => $validatedData['name'],
            'price_cents' => $priceInCents,
            'available_quantity' => $validatedData['available_quantity'],
            'is_published' => $publishStatus['is_published'],
            'publish_at' => $publishStatus['publish_at'],
            'publish_with_event' => $publishStatus['publish_with_event'],
        ]);

        $ticketType->save();

        session()->flash('message', __('Ticket type created successfully.'));
        session()->flash('message_type', 'success');

        return redirect()->route('tickettypes.show', $this->event);
    }

    protected function determinePublishStatus()
    {
        return match ($this->publish_option) {
            'publish_now' => ['is_published' => true, 'publish_at' => null, 'publish_with_event' => false],
            'schedule' => ['is_published' => false, 'publish_at' => $this->publish_at, 'publish_with_event' => false],
            'with_event' => ['is_published' => false, 'publish_at' => null, 'publish_with_event' => true],
            'draft' => ['is_published' => false, 'publish_at' => null, 'publish_with_event' => false],
        };
    }

    public function render()
    {
        return view('livewire.tickettypes.create-ticket-type');
    }
}

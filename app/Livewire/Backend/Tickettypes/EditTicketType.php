<?php

namespace App\Livewire\Backend\Tickettypes;

use App\Models\Event;
use App\Models\TicketType;
use Livewire\Component;

class EditTicketType extends Component
{
    public Event $event;
    public TicketType $ticketType;
    public string $name = '';
    public string $price_euros = '';
    public ?int $available_quantity = null;
    public bool $is_published = false;
    public ?string $publish_at = null;
    public string $publish_option = 'publish_now';
    public bool $publish_with_event = false;

    public function mount(Event $event, TicketType $ticketType)
    {
        $this->event = $event;
        $this->ticketType = $ticketType;

        // Initialize form fields
        $this->name = $ticketType->name;
        $this->price_euros = number_format($ticketType->price_cents / 100, 2);
        $this->available_quantity = $ticketType->available_quantity;
        $this->is_published = $ticketType->is_published;
        $this->publish_at = $ticketType->publish_at?->format('Y-m-d\TH:i');
        $this->publish_with_event = $ticketType->publish_with_event;

        // Set publish option based on current status
        if ($ticketType->is_published) {
            $this->publish_option = 'publish_now';
        } elseif ($ticketType->publish_at) {
            $this->publish_option = 'schedule';
        } elseif ($ticketType->publish_with_event) {
            $this->publish_option = 'with_event';
        } else {
            $this->publish_option = 'draft';
        }

        $this->authorize('tickets.update', $ticketType);
    }

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'available_quantity' =>
                ['nullable',
                    'integer',
                    'min:0',
                    function ($attribute, $value, $fail) {
                        if($this->event->max_capacity === null)
                            return;

                        $alreadyAvailableTickets = $this->event->ticketTypes->where('id', '!=', $this->ticketType->id)->sum('available_quantity');
                        if ($value > $this->event->max_capacity - $alreadyAvailableTickets) {
                            $fail(__('The available quantity must not exceed the event capacity.'));
                        }
                    },
                ],
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

        // Only able to change price if ticket type is not published and no tickets have been bought
        if (!$this->ticketType->is_published && $this->ticketType->tickets->count() == 0) {
            $rules['price_euros'] = [
                'required',
                function ($attribute, $value, $fail) {
                    $normalizedValue = str_replace(',', '.', $value);

                    if (!is_numeric($normalizedValue)) {
                        $fail(__('The price must be a valid number.'));
                        return;
                    }

                    if ($normalizedValue < 0) {
                        $fail(__('The price must be at least 0.'));
                    }
                },
            ];
        }

        return $rules;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        $this->authorize('tickets.update', $this->ticketType);
        $validatedData = $this->validate();

        $publishStatus = $this->determinePublishStatus();

        $updateData = [
            'name' => $validatedData['name'],
            'available_quantity' => $validatedData['available_quantity'],
            'is_published' => $publishStatus['is_published'],
            'publish_at' => $publishStatus['publish_at'],
            'publish_with_event' => $publishStatus['publish_with_event'],
        ];

        // Only update price if ticket type is not published and no tickets have been bought yet
        if (!$this->ticketType->is_published && $this->ticketType->tickets->count() == 0) {
            $priceInCents = (int) round(str_replace(',', '.', $validatedData['price_euros']) * 100);
            $updateData['price_cents'] = $priceInCents;
        }

        $this->ticketType->update($updateData);

        session()->flash('message', __('Ticket type updated successfully.'));
        session()->flash('message_type', 'success');

        return redirect()->route('tickettypes.show', $this->event);
    }

    protected function determinePublishStatus()
    {
        if($this->publish_option === 'with_event' && $this->event->is_published)
            $this->publish_option = 'publish_now';

        return match ($this->publish_option) {
            'publish_now' => ['is_published' => true, 'publish_at' => null, 'publish_with_event' => false],
            'schedule' => ['is_published' => false, 'publish_at' => $this->publish_at, 'publish_with_event' => false],
            'with_event' => ['is_published' => false, 'publish_at' => null, 'publish_with_event' => true],
            'draft' => ['is_published' => false, 'publish_at' => null, 'publish_with_event' => false],
            default => ['is_published' => false, 'publish_at' => null, 'publish_with_event' => false],
        };
    }

    public function render()
    {
        return view('livewire.tickettypes.edit-ticket-type');
    }
}

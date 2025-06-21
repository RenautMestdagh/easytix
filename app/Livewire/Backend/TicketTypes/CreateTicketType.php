<?php

namespace App\Livewire\Backend\TicketTypes;

use App\Http\Requests\TicketType\StoreTicketTypeRequest;
use App\Models\Event;
use App\Models\TicketType;
use App\Traits\TicketTypeManagementUtilities;
use Livewire\Component;

class CreateTicketType extends Component
{
    use TicketTypeManagementUtilities;

    public Event $event;
    public string $name = '';
    public string $price_euros = '';
    public ?int $available_quantity = null;
    public bool $is_published = false;
    public ?string $publish_at = null;
    public string $publish_option = 'publish_now';
    public bool $publish_with_event = false;

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    public function updated($propertyName)
    {
        if($propertyName === 'publish_option') {
            $this->resetErrorBag('publish_at');
            if($this->publish_option !== 'schedule')
                $this->publish_at = null;
        }

        $fieldRules = (new StoreTicketTypeRequest(
            $this->event,
            $this->publish_option,
        ))->rules();
        $fieldMessages = (new StoreTicketTypeRequest())->messages();

        if (!array_key_exists($propertyName, $fieldRules)) {
            return; // skip validation if no rule is defined
        }

        $this->validateOnly($propertyName, $fieldRules, $fieldMessages);
    }

    public function store()
    {
        if($this->publish_option !== 'schedule')
            $this->publish_at = null;

        $validatedData = $this->validate(
            (new StoreTicketTypeRequest(
                $this->event,
                $this->publish_option,
            ))->rules(),
            (new StoreTicketTypeRequest())->messages(),
        );

        $publishStatus = $this->determinePublishStatus();

        // Convert euros to cents for database storage
        $priceInCents = (int) round($validatedData['price_euros'] * 100);

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

        return redirect()->route('ticket-types.index', $this->event);
    }

    public function render()
    {
        return view('livewire.tickettypes.create-ticket-type');
    }
}

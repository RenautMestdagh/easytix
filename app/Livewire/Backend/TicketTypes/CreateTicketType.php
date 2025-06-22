<?php

namespace App\Livewire\Backend\TicketTypes;

use App\Http\Requests\TicketType\StoreTicketTypeRequest;
use App\Models\Event;
use App\Models\TicketType;
use App\Traits\FlashMessage;
use App\Traits\TicketTypeManagementUtilities;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class CreateTicketType extends Component
{
    use TicketTypeManagementUtilities, FlashMessage;

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

        try{
            TicketType::create([
                'event_id' => $this->event->id,
                'name' => $validatedData['name'],
                'price_cents' => $priceInCents,
                'available_quantity' => $validatedData['available_quantity'],
                'is_published' => $publishStatus['is_published'],
                'publish_at' => $publishStatus['publish_at'],
                'publish_with_event' => $publishStatus['publish_with_event'],
            ]);
            $this->flashMessage('Ticket type created successfully.');
            redirect()->route('ticket-types.index', $this->event);
        } catch (\Exception $e) {
            $this->flashMessage('Something went wrong, please try again later.', 'error');
            Log::error('Error creating ticket type: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.backend.tickettypes.create-ticket-type');
    }
}

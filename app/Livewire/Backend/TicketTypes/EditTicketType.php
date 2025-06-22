<?php

namespace App\Livewire\Backend\TicketTypes;

use App\Http\Requests\TicketType\UpdateTicketTypeRequest;
use App\Models\Event;
use App\Models\TicketType;
use App\Traits\FlashMessage;
use App\Traits\TicketTypeManagementUtilities;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class EditTicketType extends Component
{
    use TicketTypeManagementUtilities, FlashMessage;

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
    }

    public function updated($propertyName)
    {
        if($propertyName === 'publish_option') {
            $this->resetErrorBag('publish_at');
            if($this->publish_option !== 'schedule')
                $this->publish_at = null;
        }

        $fieldRules = (new UpdateTicketTypeRequest(
            $this->event,
            $this->publish_option,
            $this->ticketType,
        ))->rules();
        $fieldMessages = (new UpdateTicketTypeRequest())->messages();

        if (!array_key_exists($propertyName, $fieldRules)) {
            return; // skip validation if no rule is defined
        }

        $this->validateOnly($propertyName, $fieldRules, $fieldMessages);
    }

    public function update()
    {
        if($this->publish_option !== 'schedule')
            $this->publish_at = null;

        $validatedData = $this->validate(
            (new UpdateTicketTypeRequest(
                $this->event,
                $this->publish_option,
                $this->ticketType,
            ))->rules(),
            (new UpdateTicketTypeRequest())->messages(),
        );

        $publishStatus = $this->determinePublishStatus();
        $price_cents = (int) round(str_replace(',', '.', $validatedData['price_euros']) * 100);

        try{
            if($price_cents !== $this->ticketType->price_cents) {
                DB::statement('LOCK TABLES tickets WRITE');
                if($this->ticketType->tickets->count() + $this->ticketType->reservedTickets->count() > 0) {
                    DB::statement('UNLOCK TABLES');
                    $this->flashMessage('Cannot update ticket price when it has (reserved) tickets.', 'error');
                    return;
                }
                $this->ticketType->price_cents = $price_cents;
                $this->ticketType->save();
                DB::statement('UNLOCK TABLES');
            }

            $this->ticketType->update([
                'name' => $validatedData['name'],
                'available_quantity' => $validatedData['available_quantity'],
                'is_published' => $publishStatus['is_published'],
                'publish_at' => $publishStatus['publish_at'],
                'publish_with_event' => $publishStatus['publish_with_event'],
            ]);

            $this->flashMessage('Ticket type updated successfully.');
            redirect()->route('ticket-types.index', $this->event);
        }  catch (\Exception $e) {
            Log::error('Error updating ticket type: ' . $e->getMessage());
            $this->flashMessage('Error while updating ticket type.', 'error');
        } finally {
            DB::statement('UNLOCK TABLES');
        }
    }

    public function render()
    {
        return view('livewire.backend.tickettypes.edit-ticket-type');
    }
}

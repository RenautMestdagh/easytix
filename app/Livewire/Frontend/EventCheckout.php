<?php

namespace App\Livewire\Frontend;

use App\Models\Customer;
use App\Models\Event;
use App\Models\TemporaryOrder;
use Livewire\Component;
use Stripe\StripeClient;

class EventCheckout extends Component
{
    public Event $event;
    public $tempOrderId;
    public $orderTickets;
    public $orderTotal;
    public $timeRemaining;
    public $pollInterval = 60000; // Default to 1 minute (60000ms)
    public $flowStage = 2;
    private $tempOrder;

    // Customer fields
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $gender;
    public $date_of_birth;
    public $address;
    public $city;
    public $country;

    protected $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:255',
        'gender' => 'nullable|in:male,female,other,prefer not to say',
        'date_of_birth' => 'nullable|date',
        'address' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',
    ];

    public function boot()
    {
        if(!$this->tempOrderId)
            return;
        $this->tempOrder = TemporaryOrder::find($this->tempOrderId);
        if(!$this->tempOrder) {
            // temporder was expired and cleaned up by scheduler
            $this->orderExpired();
        }  else if (!$this->tempOrder->at_checkout) {
            $this->backToTickets();
        }
    }
    public function mount($subdomain, $eventuniqid)
    {
        $this->event = Event::with(['ticketTypes' => function ($query) {
            $query->where('is_published', true)->with('tickets');
        }])
            ->where('uniqid', $eventuniqid)
            ->where('is_published', true)
            ->firstOrFail();

        $this->tempOrderId = session('temporary_order_id');
        $this->tempOrder = TemporaryOrder::with('tickets.ticketType')->find($this->tempOrderId);

        if (!$this->tempOrder->at_checkout) {
            $this->backToTickets();
        }

        if(!$this->tempOrder || $this->tempOrder->isExpired()) {
            $this->orderExpired();
        }

        $this->orderTickets = $this->tempOrder->tickets
            ->groupBy('ticket_type_id')
            ->sortBy(function ($tickets, $ticketTypeId) {
                return $ticketTypeId;
            })
            ->map(function ($tickets) {
                $firstTicket = $tickets->first();
                return (object) [
                    'name' => $firstTicket->ticketType->name,
                    'price_cents' => $firstTicket->ticketType->price_cents,
                    'amount' => $tickets->count(),
                ];
            })
            ->values();

        $this->orderTotal = $this->orderTickets->sum(function($ticket) {
            return $ticket->price_cents * $ticket->amount;
        });

        $this->updateTimeRemaining();
    }

    public function newTemporaryOrder()
    {
        redirect()->route('event.tickets', [$this->event->organization->subdomain, $this->event->uniqid]);
    }

    public function orderExpired()
    {
        $basketIds = array_diff(session('basket_id', []), [$this->tempOrderId]);
        session(['basket_id' => $basketIds]);
        $this->timeRemaining = 'EXPIRED';
        $this->pollInterval = 999999999; // No need to poll if expired
    }

    public function updateTimeRemaining()
    {
        $seconds = max(0, $this->tempOrder->expires_at->timestamp - now()->timestamp);

        if ($seconds <= 0)
            return $this->orderExpired();

        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;

        // Update polling interval based on remaining time
        if ($minutes >= 2) {
            // Format minutes (singular/plural)
            $minutesText = $minutes == 1 ? '1 minute' : "$minutes minutes";
            $this->timeRemaining = $minutesText;
            $this->pollInterval = 60100; // 1 minute
        } else {
            // Format seconds and minutes (singular/plural)
            $minutesText = $minutes == 1 ? '1 minute' : "$minutes minutes";
            $secondsText = $seconds == 1 ? '1 second' : "$seconds seconds";

            if ($minutes > 0) {
                $this->timeRemaining = "$minutesText $secondsText";
            } else {
                $this->timeRemaining = $secondsText;
            }
            $this->pollInterval = 1000; // 1 second
        }
    }

    public function backToTickets()
    {
        $this->tempOrder->at_checkout = false;
        $this->tempOrder->save();
        return redirect()->route('event.tickets', [$this->event->organization->subdomain, $this->event->uniqid]);
    }

    public function proceedToPayment()
    {
        $this->validate();

        // Create customer
        $customer = Customer::withoutGlobalScopes()->updateOrCreate(
            ['email' => strtolower(trim($this->email))],
            [
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'phone' => $this->phone,
                'gender' => $this->gender,
                'date_of_birth' => $this->date_of_birth,
                'address' => $this->address,
                'city' => $this->city,
                'country' => $this->country,
            ]
        );

        $stripe = new StripeClient('sk_test_51Ralh8EFHVIkIeLWalsOQYaiqGOPiHU3LvH0n3WoejvpFo6599dzBuLNZJPNeto06crQBVIdYyOn8lCrX2HRkbfu00SvivRoGB');
        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => $this->orderTotal,
            'currency' => 'eur',
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        session(['stripe_client_secret' => $paymentIntent->client_secret]);

        return redirect()->route('event.payment', [$this->event->organization->subdomain, $this->event->uniqid]);
    }

    public function render()
    {
        return view('livewire.frontend.event-ticketflow')
            ->layout('components.layouts.organization', [
                'backgroundOverride' => $this->event->background_image_url ?? null,
                'logoOverride' => $this->event->header_image_url ?? null
            ]);
    }
}

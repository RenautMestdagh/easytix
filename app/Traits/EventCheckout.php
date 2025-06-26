<?php

namespace App\Traits;

use App\Models\Event;
use App\Models\TemporaryOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait EventCheckout
{
    public Event $event;
    public $tempOrderId;
    public $tempOrder_checkout_stage;
    private $tempOrder;
    public $quantities = [];
    public $appliedDiscounts = [];
    public $discountAmount;
    public $orderTotal;

    public $timeRemaining;
    public $pollInterval = 60000; // Default to 1 minute (60000ms)
    public $toShowPartial;

    public function initialize()
    {
        if(empty($this->event)) {
            $this->toShowPartial = match(Str::afterLast(request()->route()->getName(), '.')) {
                'tickets' => 'ticket-selection',
                'checkout' => 'order-checkout',
                'payment' => 'order-payment',
                default => Str::afterLast(request()->route()->getName(), '.'),
            };
            $this->event = Event::withCount(['tickets', 'reserved_tickets'])
                ->with(['ticketTypes' => function($query) {
                    $query->published()->with('tickets');
                }])
                ->where('uniqid', request()->route('eventuniqid'))
                ->firstOrFail();

            $this->tempOrderId = session("temporary_order_id_{$this->event->uniqid}");
        }

        if (!$this->event->relationLoaded('ticketTypes') || $this->event->ticketTypes->contains('is_published', false)) {
            // needed because livewire forgets is_published filter and assigns all ticketTypes to $this->>event->ticketTypes
            $this->event->load(['ticketTypes' => function($query) {
                $query->published()->with('tickets');
            }]);
        }

        // $tempOrder = TemporaryOrder::with('tickets.ticketType')->find( session("temporary_order_id_{$eventuniqid}") );
        if($this->tempOrderId && empty($this->tempOrder))
            $this->tempOrder = TemporaryOrder::find($this->tempOrderId);

        if(empty($this->tempOrder) && $this->timeRemaining !== 'EXPIRED')
            $this->newTemporaryOrder();

        if(empty($this->tempOrder))
            return;

        if($this->tempOrder->isExpired())
            return $this->orderExpired();

        $this->tempOrder_checkout_stage = $this->tempOrder->checkout_stage;

        if (empty($this->quantities)) {
            $this->quantities = $this->event->ticketTypes->mapWithKeys(function ($ticket) {
                return [
                    $ticket->id => (object) [
                        'name' => $ticket->name,
                        'price_cents' => $ticket->price_cents,
                        'amount' => $this->tempOrder->tickets
                            ->where('ticket_type_id', $ticket->id)
                            ->count()
                    ]
                ];
            })->all();

            if($this->tempOrder->checkout_stage > 0) {
                $this->loadAppliedDiscounts();
                $this->calculateOrderTotal();
            }
        }

        if($this->tempOrder->checkout_stage < 3)
            $this->updateTimeRemaining();
    }

    public function checkCorrectFlow()
    {
        // Livewire calls pass here as well, they dont contain subdomain variable.
        $subdomain = request()->route('subdomain') ?? $this->event->organization->subdomain;

        $correctUrl = $this->event->ticket_url;   // checkout_stage 0 => Tickets kiezen
        if($this->tempOrder->checkout_stage > 0)
            $correctUrl = $this->event->checkout_url;  // checkout_stage 1 => Persoonlijke info
        if($this->tempOrder->checkout_stage > 1 && $this->tempOrder->customer_id)
            $correctUrl = $this->event->payment_url;  // checkout_stage 2 => Stripe    |      On press of pay button => checkout_stage 3
        if($this->tempOrder->checkout_stage > 3 && $this->tempOrder->customer_id)
            $correctUrl = $this->event->confirmation_url;  // checkout_stage 4 => Order processing, failed or succeeded
        $redirect = $this->safeRedirect($correctUrl);
        return !$redirect;
    }

    public function orderExpired()
    {
        session()->forget("temporary_order_id_{$this->event->uniqid}");
        $this->timeRemaining = 'EXPIRED';
        $this->tempOrder?->delete();

    }

    public function newTemporaryOrder() {
        $this->tempOrder = TemporaryOrder::create([
            'event_id' => $this->event->id,
            'checkout_stage' => 0,
        ]);
        $this->tempOrderId = $this->tempOrder->id;
        session()->put("temporary_order_id_{$this->event->uniqid}", $this->tempOrder->id);
    }

    public function updateTimeRemaining()
    {
        if ($this->tempOrder->tickets->count() == 0) {
            $this->tempOrder->resetExpiry();
        }

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

    protected function calculateOrderTotal()
    {
        $subtotal = collect($this->quantities)->sum(function($ticket) {
            return $ticket->price_cents * $ticket->amount;
        });

        if (!$this->appliedDiscounts->isEmpty()) {
            // Check that all discounts are of the same type
            $sum_percentage = $this->appliedDiscounts->sum('discount_percent');
            $sum_fixed = $this->appliedDiscounts->sum('discount_fixed_cents');

            if (!empty($sum_percentage) && !empty($sum_fixed)) {
                throw new \Exception('All discounts in an order must be of the same type');
            }

            if (!empty($sum_percentage)) {
                $this->discountAmount = $subtotal * ($sum_percentage / 100);
            } else if (!empty($sum_fixed)) {
                $this->discountAmount = $sum_fixed;
            }
        } else {
            $this->discountAmount = 0;
        }

        $orderTotal = $subtotal - $this->discountAmount;
        $this->orderTotal = round($orderTotal);
    }

    protected function loadAppliedDiscounts()
    {
        $this->appliedDiscounts = $this->tempOrder->discountCodes()->get();
    }

    /**
     * Only redirect if we're not already on the target route
     *
     * @param Request $request
     * @param string $routeName
     * @param array $parameters
     * @return \Illuminate\Http\RedirectResponse|void
     */
    protected function safeRedirect(string $url)
    {
        $requestUrl = request()->url();
        if( request()->is('livewire/*') )
            $requestUrl = request()->header('Referer');

        if ($requestUrl !== $url) {
            return redirect($url);
        }
    }
}

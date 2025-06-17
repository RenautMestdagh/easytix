<?php

namespace App\Traits;

use App\Models\Event;
use App\Models\TemporaryOrder;
use Illuminate\Http\Request;

trait NavigateEventCheckout
{
    public Event $event;
    public $tempOrderId;
    public $tempOrder_checkout_stage;
    private $tempOrder;
    public $quantities = [];
    public $orderTotal;

    public $timeRemaining;
    public $pollInterval = 60000; // Default to 1 minute (60000ms)
    public function checkCorrectFlow()
    {
        // only gets executed on page load
        if(empty($this->event)) {
            $this->event = Event::with(['ticketTypes' => function($query) {
                $query->where('is_published', true)->with('tickets');
            }])
                ->where('uniqid', request()->route('eventuniqid'))
                ->firstOrFail();

            $this->tempOrderId = session("temporary_order_id_{$this->event->uniqid}");
        }

        // $tempOrder = TemporaryOrder::with('tickets.ticketType')->find( session("temporary_order_id_{$eventuniqid}") );
        if($this->tempOrderId && empty($this->tempOrder))
            $this->tempOrder = TemporaryOrder::find($this->tempOrderId);

        if(empty($this->tempOrder))
            $this->newTemporaryOrder();

        if($this->tempOrder->isExpired())
            return $this->orderExpired();

        $this->tempOrder_checkout_stage = $this->tempOrder->checkout_stage;

        // only gets executed on page load
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

            $this->orderTotal = collect($this->quantities)->sum(function($ticket) {
                return $ticket->price_cents * $ticket->amount;
            });
        }

        // Livewire calls pass here as well, they dont contain subdomain variable.
        $subdomain = request()->route('subdomain') ?? $this->event->organization->subdomain;

        $correctUrl = route('event.tickets', [$subdomain, $this->event->uniqid]);   // checkout_stage 0 => Tickets kiezen
        if($this->tempOrder->checkout_stage>0)
            $correctUrl = route('event.checkout', [$subdomain, $this->event->uniqid]);  // checkout_stage 1 => Persoonlijke info
        if($this->tempOrder->checkout_stage>1 && $this->tempOrder->payment_intent_id && $this->tempOrder->customer_id)
            $correctUrl = route('event.payment', [$subdomain, $this->event->uniqid]);  // checkout_stage 2 => Stripe
        $this->safeRedirect($correctUrl);

        $this->updateTimeRemaining();
    }

    public function orderExpired()
    {
        session()->forget("temporary_order_id_{$this->event->uniqid}");
        $this->timeRemaining = 'EXPIRED';
    }

    public function newTemporaryOrder() {
        $this->tempOrder = TemporaryOrder::create([
            'event_id' => $this->event->id,
            'checkout_stage' => 0,
        ]);
        $this->tempOrderId = $this->tempOrder->id;
        session()->put("temporary_order_id_{$this->event->uniqid}", $this->tempOrder->id);
    }

    public function moveForwardInCheckout()
    {
        $this->tempOrder->checkout_stage++;
        $this->tempOrder->save();
    }

    public function goBackInCheckout()
    {
        // Implementation here
        return "Result from shared function 1";
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

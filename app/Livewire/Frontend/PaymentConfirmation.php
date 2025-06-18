<?php

namespace App\Livewire\Frontend;

use App\Jobs\CheckTemporaryOrderStatus;
use App\Mail\OrderConfirmationMail;
use App\Models\Event;
use App\Models\Order;
use App\Models\TemporaryOrder;
use App\Traits\NavigateEventCheckout;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Stripe\StripeClient;

class PaymentConfirmation extends Component
{
    use NavigateEventCheckout;

    public $redirect_status;

    public function mount($subdomain, $eventuniqid)
    {
        // Beetje beveiliging tegen sjoemelaars maar referer header is nog te spoofen
        if(request()->header('Referer') !== "https://payments.stripe.com/")
            return redirect('/');

        $this->toShowPartial = Str::afterLast(request()->route()->getName(), '.');
        $this->event = Event::with(['ticketTypes' => function($query) {
            $query->where('is_published', true)->with('tickets');
        }])
            ->where('uniqid', $eventuniqid)
            ->firstOrFail();

        $this->tempOrderId = session("temporary_order_id_{$eventuniqid}");
        $this->tempOrder = TemporaryOrder::find($this->tempOrderId);
        session()->forget("temporary_order_id_{$eventuniqid}");

        if($this->tempOrder) {
            $this->tempOrder->checkout_stage = 4;
            $this->tempOrder->save();
            if(!$this->checkCorrectFlow())
                return;
        }
        $this->tempOrder_checkout_stage = 4;

        $pament_id = request('payment_intent');
        $this->redirect_status = request('redirect_status');

         CheckTemporaryOrderStatus::dispatch($pament_id);
    }

    public function backToPayment()
    {
        $this->tempOrder = TemporaryOrder::find($this->tempOrderId);
        if($this->tempOrder) {
            $this->tempOrder->checkout_stage = 2;
            $this->tempOrder->save();
            session()->put("temporary_order_id_{$this->event->uniqid}", $this->tempOrder->id);

            return redirect()->route('event.payment', [$this->event->organization->subdomain, $this->event->uniqid]);
        } else {
            throw new Exception('Order not found');
        }
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

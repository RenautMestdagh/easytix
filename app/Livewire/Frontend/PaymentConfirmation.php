<?php

namespace App\Livewire\Frontend;

use App\Jobs\CheckTemporaryOrderStatus;
use App\Models\Event;
use App\Models\TemporaryOrder;
use App\Traits\FlashMessage;
use App\Traits\NavigateEventCheckout;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;

class PaymentConfirmation extends Component
{
    use NavigateEventCheckout, FlashMessage;

    public $redirect_status;

    public function mount($subdomain, $eventuniqid)
    {
        // Beetje beveiliging tegen sjoemelaars maar referer header is nog te spoofen
        if(
            request()->header('Referer') !== route('event.payment', [$subdomain, $eventuniqid]) &&
            request()->header('Referer') !== "https://payments.stripe.com/" &&
            !session()->pull('payment_succeeded', false)
        ) {
            return redirect('/');
        }


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
            if($this->tempOrder->checkout_stage == 3) {
                $this->tempOrder->checkout_stage = 4;
                $this->tempOrder->save();
                CheckTemporaryOrderStatus::dispatch(request('payment_intent'));
            }
            if(!$this->checkCorrectFlow())
                return;
        }
        $this->tempOrder_checkout_stage = 4;
        $this->redirect_status = request('redirect_status');
    }

    public function backToPayment()
    {
        try {
            $this->tempOrder = TemporaryOrder::findOrFail($this->tempOrderId);
        } catch (\Exception $e) {
            Log::error('Temporary order not found in payment confirmation: ' . $e->getMessage());
            $this->redirect_status = 'temporary_order_not_found';
            return;
        }

        $this->tempOrder->checkout_stage = 2;
        try {
            $this->tempOrder->save();
            session()->put("temporary_order_id_{$this->event->uniqid}", $this->tempOrder->id);
            redirect()->route('event.payment', [$this->event->organization->subdomain, $this->event->uniqid]);
        } catch (\Exception $e) {
            Log::error('Error backing to payment: ' . $e->getMessage());
            $this->flashMessage('An error occurred, please try again.', 'error');
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

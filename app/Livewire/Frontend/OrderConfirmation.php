<?php

namespace App\Livewire\Frontend;

use App\Jobs\CheckTemporaryOrderStatus;
use App\Jobs\ProcessSuccessfulOrder;
use App\Models\Event;
use App\Models\TemporaryOrder;
use App\Traits\EventCheckout;
use App\Traits\FlashMessage;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class OrderConfirmation extends Component
{
    use EventCheckout, FlashMessage;

    public $redirect_status;

    public function mount($subdomain, $eventuniqid)
    {
        // Beetje beveiliging tegen sjoemelaars maar referer header is nog te spoofen
        if(
            request()->header('Referer') !== route('event.payment', [$subdomain, $eventuniqid]) &&
            request()->header('Referer') !== "https://payments.stripe.com/" &&
            !session('payment_succeeded', false)
        ) {
            throw new AccessDeniedHttpException();
        }


        $this->toShowPartial = 'order-confirmation';
        $this->event = Event::with(['ticketTypes' => function($query) {
            $query->where('is_published', true)->with('tickets');
        }])
            ->where('uniqid', $eventuniqid)
            ->firstOrFail();

        $this->tempOrderId = session("temporary_order_id_{$eventuniqid}");
        $this->tempOrder = TemporaryOrder::find($this->tempOrderId);
        session()->forget("temporary_order_id_{$eventuniqid}");
        session()->forget("payment_succeeded");

        if($this->tempOrder) {
            if($this->tempOrder->checkout_stage == 3) {
                // If you come from stripe checkout
                $this->tempOrder->checkout_stage = 4;
                $this->tempOrder->save();
                CheckTemporaryOrderStatus::dispatch(request('payment_intent'));
            } else if($this->tempOrder->checkout_stage == 5) {
                // Order total was 0
                ProcessSuccessfulOrder::dispatch($this->tempOrder, null);
                $this->redirect_status = 'free';
            }
            if(!$this->checkCorrectFlow())
                return;
        }
        $this->tempOrder_checkout_stage = 4;
        if(!$this->redirect_status)
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
            redirect($this->event->payment_url);
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

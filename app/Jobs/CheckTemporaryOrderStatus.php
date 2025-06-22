<?php

namespace App\Jobs;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\TemporaryOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\StripeClient;

class CheckTemporaryOrderStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $paymentIntentId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $paymentIntentId)
    {
        $this->paymentIntentId = $paymentIntentId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $stripe = new StripeClient(config('app.stripe.secret'));

        $paymentIntent = null;
        try {
            $paymentIntent = $stripe->paymentIntents->retrieve($this->paymentIntentId);
        } catch (\Exception $e) {
            Log::error("Failed to retreive PaymentIntent: {$e->getMessage()}");
            $this->fail($e);
        }

        $tempOrder = TemporaryOrder::where('payment_id', $this->paymentIntentId)->first();
        if (!$tempOrder) {
            Log::error("Order not found for PaymentIntent: {$this->paymentIntentId}");
            return;
        }

        switch ($paymentIntent->status) {
            case 'succeeded':

                try {
                    // Wrap in DB transaction
                    DB::beginTransaction();

                    $order = Order::create([
                        'customer_id' => $tempOrder->customer_id,
                        'payment_id' => $paymentIntent->id,
                    ]);

                    $tempOrder->tickets()->update([
                        'order_id' => $order->id,
                        'temporary_order_id' => null
                    ]);
                    $tempOrder->discountCodes()->update([
                        'order_id' => $order->id,
                        'temporary_order_id' => null
                    ]);
                    $tempOrder->delete();

                    DB::commit();

                    // Send confirmation email
                    Mail::to($order->customer->email)
                        ->send(new OrderConfirmationMail($order->fresh()->load('tickets')));

                } catch (\Exception $e) {
                    DB::rollBack();

                    Log::error("Failed to process successful PaymentIntent: {$e->getMessage()}", [
                        'paymentIntentId' => $paymentIntent->id,
                        'orderId' => $order->id ?? null,
                    ]);

                    if ($this->attempts() < 5)
                        $this->release(5);
                    else
                        $this->fail($e);
                }

                break;
            case 'processing':
            case 'requires_action':
            case 'requires_payment_method':
            case 'canceled':
                break;
            default:
                Log::warning("Unhandled PaymentIntent status: {$paymentIntent->status}");
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("CheckTemporaryOrderStatus job failed for PaymentIntent: {$this->paymentIntentId}", [
            'error' => $exception->getMessage()
        ]);
    }
}

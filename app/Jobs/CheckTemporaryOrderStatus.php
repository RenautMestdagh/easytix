<?php

namespace App\Jobs;

use App\Mail\OrderConfirmationMail;
use App\Models\TemporaryOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Stripe\StripeClient;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

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

        try {
            $paymentIntent = $stripe->paymentIntents->retrieve($this->paymentIntentId);

            // Find the associated order (you might need to adjust this based on your DB structure)
            $tempOrder = TemporaryOrder::where('payment_intent_id', $this->paymentIntentId)->first();

            if (!$tempOrder) {
                Log::error("Order not found for PaymentIntent: {$this->paymentIntentId}");
                return;
            }

            switch ($paymentIntent->status) {
                case 'succeeded':
                    $order = Order::create([
                        'customer_id' => $tempOrder->customer_id,
                        'payment_intent_id' => $paymentIntent->id,
                    ]);

                    $tempOrder->tickets()->update([
                        'order_id' => $order->id,
                        'temporary_order_id' => null
                    ]);

                    // Send confirmation email
                    Mail::to($tempOrder->customer->email)
                        ->send(new OrderConfirmationMail($order->fresh()->load('tickets')));

                    $tempOrder->delete();
                    break;
//                case 'processing':
//                case 'requires_action':
//                case 'requires_payment_method':
//                case 'canceled':
                default:
                    Log::warning("Unhandled PaymentIntent status: {$paymentIntent->status}");
            }

        } catch (\Exception $e) {
            Log::error("Failed to check PaymentIntent status: {$e->getMessage()}");
            $this->fail($e);
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

        // Optional: Notify admin or perform cleanup
    }
}

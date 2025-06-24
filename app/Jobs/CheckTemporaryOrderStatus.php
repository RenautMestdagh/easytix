<?php

namespace App\Jobs;

use App\Models\TemporaryOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
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
        if(!$this->paymentIntentId)
            return;

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
                ProcessSuccessfulPayment::dispatch($tempOrder, $paymentIntent);
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

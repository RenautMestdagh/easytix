<?php

namespace App\Jobs;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessSuccessfulOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The temporary order instance.
     *
     * @var mixed
     */
    protected $tempOrder;

    /**
     * The payment intent instance.
     *
     * @var mixed
     */
    protected $paymentIntent;

    /**
     * Create a new job instance.
     *
     * @param mixed $tempOrder
     * @param mixed $paymentIntent
     */
    public function __construct($tempOrder, $paymentIntent)
    {
        $this->tempOrder = $tempOrder;
        $this->paymentIntent = $paymentIntent;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        try {
            DB::transaction(function () {
                $order = Order::create([
                    'customer_id' => $this->tempOrder->customer_id,
                    'payment_id' => $this->paymentIntent?->id ?? null,
                ]);

                $this->tempOrder->tickets()->update([
                    'order_id' => $order->id,
                    'temporary_order_id' => null
                ]);

                $this->tempOrder->discountCodes()->update([
                    'order_id' => $order->id,
                    'temporary_order_id' => null
                ]);

                $this->tempOrder->delete();

                // Send confirmation email
                Mail::to($order->customer->email)->queue(new OrderConfirmationMail($order->fresh()->load('tickets')));
            });

        } catch (\Exception $e) {
            Log::error("Failed to process successful PaymentIntent: {$e->getMessage()}", [
                'paymentIntentId' => $this->paymentIntent?->id ?? null,
                'exception' => $e,
            ]);

            throw $e;
        }
    }
}

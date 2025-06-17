<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\TemporaryOrder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CleanExpiredOrdersJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $expiredOrders = TemporaryOrder::where(function($query) {
            $query->where('expires_at', '<', now())
                ->where('checkout_stage', '!=', 3);
        })->orWhere(function($query) {
            $query->where('checkout_stage', 3)
                ->where('expires_at', '<', now()->subDay());
        })->get();

        foreach($expiredOrders as $expiredOrder) {
            $customer = Customer::find($expiredOrder->customer_id);
            $expiredOrder->tickets()->delete();
            $expiredOrder->delete();
            if($customer?->orders->isEmpty() && $customer?->temporaryOrders->isEmpty()) {
                Customer::withoutGlobalScopes()->where('id', $customer->id)->delete();
            }
        }
    }
}

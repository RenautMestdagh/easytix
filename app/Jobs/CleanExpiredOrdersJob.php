<?php

namespace App\Jobs;

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
        $expiredOrders = TemporaryOrder::where('expires_at', '<', now())->get();

        foreach($expiredOrders as $expiredOrder) {
            $expiredOrder->tickets()->delete();
            $expiredOrder->delete();
        }
    }
}

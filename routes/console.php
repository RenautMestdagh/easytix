<?php

use App\Jobs\CheckTemporaryOrderStatus;
use App\Jobs\CleanExpiredOrdersJob;
use App\Models\TemporaryOrder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
Schedule::call(function () {
    CleanExpiredOrdersJob::dispatch();
//})->everyMinute();
})->everyFiveMinutes();   // TODO

Schedule::call(function () {
    $temporaryOrders = TemporaryOrder::where('checkout_stage', '>', 3)->get();
    foreach ($temporaryOrders as $order) {
        if($order->payment_id)
            CheckTemporaryOrderStatus::dispatch($order->payment_id);
        else
            $order->delete();
    }

//})->everyMinute();
})->everyTenMinutes();   // TODO

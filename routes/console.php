<?php

use App\Jobs\CleanExpiredOrdersJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
Schedule::call(function () {
    CleanExpiredOrdersJob::dispatch();
})->everyFiveMinutes();

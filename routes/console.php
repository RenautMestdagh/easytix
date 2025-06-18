<?php

use App\Jobs\CheckTemporaryOrderStatus;
use App\Jobs\CleanExpiredOrdersJob;
use App\Models\TemporaryOrder;
use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

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

Schedule::call(function () {
    // Publish events that are scheduled for publishing
    $eventsToPublish = Event::scheduledForPublishing()->get();

    foreach ($eventsToPublish as $event) {
        $event->update([
            'is_published' => true,
            'publish_at' => null // Clear the publish_at since it's now published
        ]);

        // Publish any ticket types that should publish with the event
        $ticketTypesToPublish = $event->ticketTypes()
            ->where('publish_with_event', true)
            ->where('is_published', false)
            ->get();

        foreach ($ticketTypesToPublish as $ticketType) {
            $ticketType->update([
                'is_published' => true,
                'publish_at' => null // Clear the publish_at since it's now published
            ]);
        }
    }

    // Publish ticket types that have their own publish schedule
    $ticketTypesToPublish = TicketType::scheduledForPublishing()
        ->where('publish_with_event', false)
        ->get();

    foreach ($ticketTypesToPublish as $ticketType) {
        $ticketType->update([
            'is_published' => true,
            'publish_at' => null // Clear the publish_at since it's now published
        ]);
    }
})->everyMinute();

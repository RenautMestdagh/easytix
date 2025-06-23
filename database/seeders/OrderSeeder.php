<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Create 50 orders with random customers and payments
        $orders = Order::factory(5000)->create();

        foreach ($orders as $order) {
            $event = Event::inRandomOrder()->first();
            $ticketTypes = $event->ticketTypes()->get();

            $pickedTypes = [];
            foreach ($ticketTypes as $ticketType) {
                if (random_int(0, 100) < 30) {
                    $pickedTypes[] = $ticketType->id;
                }
            }

            foreach ($pickedTypes as $ticketTypeId) {
                $quantity = random_int(1, 7);
                for ($i = 0; $i < $quantity; $i++) {
                    Ticket::create([
                        'order_id' => $order->id,
                        'ticket_type_id' => $ticketTypeId,
                    ]);
                }
            }
        }
    }
}

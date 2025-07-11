<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Database\Seeder;

class TicketTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = Event::all();

        foreach ($events as $event) {
            $ticketTypeCount = rand(0, 5);
            $totalCapacity = $event->capacity;

            // Create ticket types with quantities that sum to capacity
            $quantities = [];
            $remaining = $totalCapacity;

            for ($i = 0; $i < $ticketTypeCount; $i++) {
                $count = $remaining * (mt_rand(0, 150) / 100);
                if ($i === $ticketTypeCount - 1) {

                    $quantities[] = $count;
                } else {
                    $max = max(10, min(500, $remaining - ($ticketTypeCount - $i - 1) * 10));
                    $qty = fake()->numberBetween(10, $max);
                    $quantities[] = $qty;
                    $remaining -= max($qty, $count);
                }
            }

            // Create ticket types with calculated quantities
            foreach ($quantities as $quantity) {
                if($quantity <= 0) continue;
                TicketType::factory()->create([
                    'event_id' => $event->id,
                    'available_quantity' => $quantity,
                ]);
            }
        }
    }
}

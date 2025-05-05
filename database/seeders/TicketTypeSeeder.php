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
            $totalCapacity = $event->max_capacity;

            // Create ticket types with quantities that sum to max_capacity
            $quantities = [];
            $remaining = $totalCapacity;

            for ($i = 0; $i < $ticketTypeCount; $i++) {
                if ($i === $ticketTypeCount - 1) {
                    $quantities[] = $remaining;
                } else {
                    $max = max(10, min(500, $remaining - ($ticketTypeCount - $i - 1) * 10));
                    $qty = fake()->numberBetween(10, $max);
                    $quantities[] = $qty;
                    $remaining -= $qty;
                }
            }

            // Create ticket types with calculated quantities
            foreach ($quantities as $quantity) {
                TicketType::factory()->create([
                    'event_id' => $event->id,
                    'available_quantity' => $quantity,
                ]);
            }
        }
    }
}

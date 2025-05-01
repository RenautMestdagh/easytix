<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all events
        $events = Event::all();

        foreach ($events as $event) {
            // Create between 0 and 5 ticket types for each event
            $ticketTypeCount = rand(0, 5);

            TicketType::factory($ticketTypeCount)->create([
                'event_id' => $event->id,
            ]);
        }
    }
}

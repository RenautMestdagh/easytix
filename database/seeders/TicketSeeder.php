<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all ticket types
        $ticketTypes = TicketType::all();

        foreach ($ticketTypes as $ticketType) {
            // Create between 0 and 50 tickets for each ticket type
            $ticketCount = rand(0, 50);

            Ticket::factory($ticketCount)->create([
                'ticket_type_id' => $ticketType->id,
            ]);
        }
    }
}

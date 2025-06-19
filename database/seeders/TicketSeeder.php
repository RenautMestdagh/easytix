<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketType;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $ticketTypes = TicketType::withCount('tickets')->get();

        foreach ($ticketTypes as $ticketType) {
            $remainingCapacity = $ticketType->available_quantity - $ticketType->tickets_count;

            if ($remainingCapacity > 0) {
                // Create tickets in batches to avoid capacity issues
                $batchSize = min(10, $remainingCapacity);
                $batches = ceil($remainingCapacity / $batchSize);

                for ($i = 0; $i < $batches; $i++) {
                    $currentBatchSize = min($batchSize, $remainingCapacity - ($i * $batchSize));

                    Ticket::factory($currentBatchSize)->create([
                        'ticket_type_id' => $ticketType->id,
                    ]);
                }
            }
        }
    }
}

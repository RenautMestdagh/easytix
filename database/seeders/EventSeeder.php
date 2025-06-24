<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all organizer users
        $organizations = Organization::all();

        foreach ($organizations as $organization) {
            // Create between 2 and 20 events for each organizer
            $eventCount = rand(2, 20);
            for ($i = 0; $i < $eventCount; $i++) {
                $event = Event::factory()->create([
                    'organization_id' => $organization->id,
                ]);

                if($i <= 1) {
                    $event->subdomain = "event" . ($i + 1);
                    $event->save();
                }
            }
        }
    }
}

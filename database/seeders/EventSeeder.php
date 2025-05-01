<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            // Create between 0 and 10 events for each organizer
            $eventCount = rand(0, 10);
            Event::factory($eventCount)->create([
                'organization_id' => $organization->id,
            ]);
        }
    }
}

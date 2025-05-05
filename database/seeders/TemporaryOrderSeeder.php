<?php

namespace Database\Seeders;

use App\Models\TemporaryOrder;
use Illuminate\Database\Seeder;

class TemporaryOrderSeeder extends Seeder
{
    public function run(): void
    {
        // Create 100 temporary orders
        TemporaryOrder::factory(100)->create();

        // Create some confirmed temporary orders
        TemporaryOrder::factory(20)->create([
            'is_confirmed' => true,
        ]);

        // Create some expired temporary orders
        TemporaryOrder::factory(15)->create([
            'expires_at' => now()->subHours(1),
        ]);
    }
}

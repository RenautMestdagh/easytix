<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Create 50 orders with random customers and payments
        Order::factory(50)->create();

        // Or create orders with specific conditions
        Order::factory(10)->create([
            'customer_id' => Customer::where('email', 'like', '%@example.com')->first()->id,
        ]);
    }
}

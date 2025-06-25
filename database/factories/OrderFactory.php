<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'payment_id' => uniqid(),
            'created_at' => now()->subDays(random_int(0, 30)),
        ];
    }
}

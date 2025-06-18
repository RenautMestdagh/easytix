<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uniqid' => str_replace('-', '', Str::uuid()),
            'customer_id' => Customer::factory(),
            'payment_id' => uniqid(),
        ];
    }
}

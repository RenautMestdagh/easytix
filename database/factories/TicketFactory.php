<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    public function definition()
    {
        return [
            'order_id' => Order::inRandomOrder()->first()->id,
            'temporary_order_id' => null,
            'ticket_type_id' => TicketType::inRandomOrder()->first(),
            'scanned_at' => $this->faker->boolean(20) ? $this->faker->dateTimeThisYear() : null,
        ];
    }
}

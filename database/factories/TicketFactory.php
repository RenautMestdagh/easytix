<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\TemporaryOrder;
use App\Models\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    public function definition()
    {

        return [
            'order_id' => rand(0, 1) ? Order::inRandomOrder()->first()->id : null,
            'temporary_order_id' => rand(0, 1) ? TemporaryOrder::inRandomOrder()->first()->id : null,
            'ticket_type_id' => TicketType::inRandomOrder()->first(),
            'qr_code' => $this->faker->unique()->uuid(),
            'scanned_at' => $this->faker->boolean(20) ? $this->faker->dateTimeThisYear() : null,
        ];
    }
}

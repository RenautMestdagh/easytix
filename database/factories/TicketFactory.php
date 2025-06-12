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
        $number = str_pad(random_int(0, 9999999999999), 13, '0', STR_PAD_LEFT);
        return [
            'order_id' => rand(0, 1) ? Order::inRandomOrder()->first()->id : null,
            'temporary_order_id' => rand(0, 1) ? TemporaryOrder::inRandomOrder()->first()->id : null,
            'ticket_type_id' => TicketType::inRandomOrder()->first(),
            'qr_code' => $number,
            'scanned_at' => $this->faker->boolean(20) ? $this->faker->dateTimeThisYear() : null,
        ];
    }
}

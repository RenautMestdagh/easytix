<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'payment_id' => Payment::inRandomOrder()->first()->id, // Random payment
            'ticket_type_id' => TicketType::inRandomOrder()->first()->id, // Random ticket type
            'customer_id' => Customer::inRandomOrder()->first()->id, // Random customer
            'qr_code' => $this->faker->unique()->uuid(), // Unique QR code (UUID)
            'scanned_at' => $this->faker->boolean() ? $this->faker->dateTimeThisYear() : null, // Random scanned time (nullable)
        ];
    }
}

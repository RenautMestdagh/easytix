<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketType>
 */
class TicketTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::inRandomOrder()->first()->id, // Random event
            'name' => $this->faker->word(), // Name of the ticket type (e.g., VIP, General Admission)
            'price_cents' => $this->faker->numberBetween(1000, 50000), // Random price in cents
            'available_quantity' => $this->faker->numberBetween(10, 500), // Random available quantity
        ];
    }
}

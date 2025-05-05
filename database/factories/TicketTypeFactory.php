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
    // TicketTypeFactory.php
    public function definition(): array
    {
        return [
            'event_id' => Event::inRandomOrder()->first()->id,
            'name' => $this->faker->word(),
            'price_cents' => $this->faker->numberBetween(1000, 50000),
            'available_quantity' => $this->faker->numberBetween(10, 500),
            'is_published' => $this->faker->boolean,
            'publish_at' => $this->faker->boolean(30) ? $this->faker->dateTimeBetween('-1 week', '+1 month') : null,
            'publish_with_event' => $this->faker->boolean(20),
        ];
    }
}

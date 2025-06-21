<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiscountCode>
 */
class DiscountCodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $discount_type = $this->faker->boolean(); // Randomly pick between 0 (false) and 1 (true)
        $event = Event::inRandomOrder()->first();

        $start_date = $this->faker->boolean() ? $this->faker->dateTimeBetween('-1 week', $event->date) : null;
        $end_date = $this->faker->boolean() ? $this->faker->dateTimeBetween($start_date, $event->date) : null;

        return [
            'organization_id' => $event->organization->id,
            'code' => $this->faker->unique()->word, // Unique discount code
            'event_id' => $event->id, // Random event
            'start_date' => $start_date,
            'end_date' => $end_date,
            'max_uses' => $this->faker->boolean() ? $this->faker->numberBetween(10, 100) : null, // Random max uses
            'discount_percent' => $discount_type ? $this->faker->numberBetween(5, 50) : null, // Percent if true
            'discount_fixed_cents' => !$discount_type ? $this->faker->numberBetween(100, 5000) : null, // Fixed amount if false
        ];
    }
}

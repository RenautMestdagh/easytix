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

        return [
            'event_id' => Event::inRandomOrder()->first()->id, // Random event
            'organization_id' => Organization::inRandomOrder()->first()->id,
            'code' => $this->faker->unique()->word, // Unique discount code
            'discount_percent' => $discount_type ? $this->faker->numberBetween(5, 50) : null, // Percent if true
            'discount_fixed_cents' => !$discount_type ? $this->faker->numberBetween(100, 5000) : null, // Fixed amount if false
            'max_uses' => $this->faker->boolean() ? $this->faker->numberBetween(10, 100) : null, // Random max uses
        ];
    }
}

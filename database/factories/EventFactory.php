<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    // EventFactory.php
    public function definition()
    {
        return [
            'organization_id' => Organization::inRandomOrder()->first()->id,
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'location' => $this->faker->address,
            'date' => $this->faker->dateTimeThisYear(),
            'event_image' => null,
            'header_image' => null,
            'background_image' => null,
            'max_capacity' => $this->faker->numberBetween(50, 500),
            'is_published' => $this->faker->boolean,
            'publish_at' => $this->faker->boolean(30) ? $this->faker->dateTimeBetween('-1 week', '+1 month') : null,
        ];
    }
}

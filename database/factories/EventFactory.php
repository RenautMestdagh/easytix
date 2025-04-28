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
    public function definition()
    {
        return [
            'organizer_id' => Organization::inRandomOrder()->first()->id, // Assigning random organization
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'location' => $this->faker->address,
            'date' => $this->faker->dateTimeThisYear(),
            'banner_image' => $this->faker->imageUrl(),
            'max_capacity' => $this->faker->numberBetween(50, 500),
        ];
    }
}

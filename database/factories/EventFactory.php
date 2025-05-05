<?php

namespace Database\Factories;

use App\Models\Organization;
use DateTime;
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
        $date = $this->faker->dateTimeBetween('-1 week', '+1 month');

        // 30% chance of having a publish_at date (auto-publish)
        $publishAt = $this->faker->boolean(30)
            ? $this->faker->dateTimeBetween('-1 week', $date)
            : null;

        // Determine if published (either auto-published or manually published)
        $isPublished = $this->faker->boolean(70) || ($publishAt && $publishAt <= new DateTime());

        return [
            'organization_id' => Organization::inRandomOrder()->first()->id,
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph,
            'location' => $this->faker->address,
            'date' => $date,
            'event_image' => null,
            'header_image' => null,
            'background_image' => null,
            'max_capacity' => $this->faker->numberBetween(50, 500),
            'is_published' => $isPublished,
            'publish_at' => $publishAt,
        ];
    }
}

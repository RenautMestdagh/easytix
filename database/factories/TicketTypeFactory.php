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
    public function definition()
    {
        $event = Event::inRandomOrder()->first();
        $date = $event->date;

        // Decide which publishing strategy to use (30% chance for publish_at, 70% for publish_with_event)
        $usePublishAt = $this->faker->boolean(30);

        // Set publish_at only if we're using publish_at strategy
        $publishAt = $usePublishAt ? $this->faker->dateTimeBetween('-1 week', $date) : null;

        // Set publish_with_event to true only if we're not using publish_at
        $publishWithEvent = !$usePublishAt;

        // Determine published status
        $isPublished = false;
        if ($usePublishAt) {
            $isPublished = $publishAt <= new \DateTime();
        } else {
            $isPublished = $event->is_published;
        }

        return [
            'event_id' => $event->id,
            'name' => $this->faker->word(),
            'price_cents' => $this->faker->numberBetween(1000, 50000),
            'available_quantity' => $this->faker->numberBetween(10, 500),
            'is_published' => $isPublished,
            'publish_at' => $publishAt,
            'publish_with_event' => $publishWithEvent,
        ];
    }
}

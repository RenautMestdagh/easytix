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

        $publishAt = $this->faker->boolean(30)
            ? $this->faker->dateTimeBetween('-1 week', $date)
            : null;

        $isPublished = $publishAt && $publishAt <= new \DateTime();
        $publishWithEvent = $this->faker->boolean(20);

        // If publishing with event, inherit event's published status
        if ($publishWithEvent && $event->is_published) {
            $isPublished = true;
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

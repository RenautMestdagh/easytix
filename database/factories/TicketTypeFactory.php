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

        $isPublished = false;
        $publishAt = null;
        $publishWithEvent = null;
        if($event->is_published) {
            $isPublished = true;
            $publishAt = null;
            $publishWithEvent = false;
        } else {
            $isPublished = false;

            // Decide which publishing strategy to use (publish_at or publish_with_event)
            $usePublishAt = $this->faker->boolean();

            // Set publish_at only if we're using publish_at strategy
            $publishAt = $usePublishAt ? $this->faker->dateTimeBetween('-1 week', $date) : null;
            
            if ($usePublishAt)
                $isPublished = $publishAt <= new \DateTime();

            // Set publish_with_event to true only if we're not using publish_at
            $publishWithEvent = !$usePublishAt;
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

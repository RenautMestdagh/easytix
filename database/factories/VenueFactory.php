<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Venue>
 */
class VenueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'organization_id' => Organization::inRandomOrder()->first()->id,
            'coordinates' => null,
        ];
    }

    /**
     * Set the organization for the venue.
     *
     * @param  int  $organizationId
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forOrganization(int $organizationId)
    {
        return $this->state(function (array $attributes) use ($organizationId) {
            return [
                'organization_id' => $organizationId,
            ];
        });
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $index = 1; // Counter to generate unique subdomains

        // Customize subdomains for index 1 and 2
        $subdomains = [
            1 => 'kompass',
            2 => 'modulair',
        ];

        // Use the predefined subdomain for index 1 and 2, otherwise use default logic
        $subdomain = $subdomains[$index++] ?? 'subdomain' . $index++;

        return [
            'name' => $this->faker->company,
            'subdomain' => $subdomain,
        ];
    }
}

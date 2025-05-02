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
        static $index = 1;

        $customOrganizations = [
            1 => ['name' => "Kompass Klub", 'subdomain' => "kompass"],
            2 => ['name' => "Modul'air", 'subdomain' => "modulair"],
        ];

        $organization = $customOrganizations[$index] ?? [
            'name' => $this->faker->company,
            'subdomain' => 'subdomain' . $index,
        ];

        $index++;

        return $organization;
    }
}

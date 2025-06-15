<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TemporaryOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'expires_at' => Carbon::now()->addHours(2),
            'is_confirmed' => $this->faker->boolean(),
        ];
    }
}

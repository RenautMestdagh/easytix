<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'customer_email' => $this->faker->unique()->safeEmail(),
            'customer_name' => $this->faker->name(),
            'amount_cents' => $this->faker->numberBetween(1000, 100000), // Amount in cents
            'payment_method' => $this->faker->randomElement(['stripe', 'paypal']),
            'payment_status' => $this->faker->randomElement(['pending', 'succeeded', 'failed']),
            'transaction_id' => $this->faker->optional()->uuid(),
        ];
    }
}

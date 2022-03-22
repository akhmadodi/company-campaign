<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'total_spent' => $this->faker->numberBetween(5, 500),
            'total_saving' => $this->faker->numberBetween(5, 500),
            'transaction_at' => $this->faker->unique()->dateTimeBetween('-90 days', '-1 days'),
        ];
    }
}

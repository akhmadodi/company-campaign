<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // return [
        //     'name' => $this->faker->name(),
        //     'start_at' => $this->faker->unique()->dateTimeBetween('7 days', 'now'),
        //     'ended_at' => $this->faker->unique()->dateTimeBetween('now', '30 days'),
        //     'min_purchase_transactions' => $this->faker->numberBetween(1, 10),
        //     'in_last_days' => $this->faker->numberBetween(1, 30),
        //     'total_transactions' => $this->faker->numberBetween(5, 500),
        //     'total_vouchers' => $this->faker->numberBetween(5, 1000),
        // ];

        return [
            'name' => $this->faker->name(),
            'start_at' => $this->faker->unique()->dateTimeBetween('now', '3 days'),
            'ended_at' => $this->faker->unique()->dateTimeBetween('now', '30 days'),
            'min_purchase_transactions' => 3,
            'in_last_days' => 30,
            'total_transactions' => 100,
            'total_vouchers' => 3,
        ];
    }
}

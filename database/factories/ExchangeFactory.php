<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exchange>
 */
class ExchangeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $def = [];

        foreach (config('exchange.currencies') as $cur) {
            $def[$cur] = $this->faker->numberBetween(1, 50);
        }

        return $def;
    }
}

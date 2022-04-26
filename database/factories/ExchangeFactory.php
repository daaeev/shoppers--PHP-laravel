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
        $def['UAH'] = 1;

        $currs = config('exchange.currencies', [config('exchange.base', 'UAH')]);
        unset($currs[config('exchange.base', 'UAH')]);

        foreach ($currs as $cur) {
            $def[$cur] = $this->faker->numberBetween(1, 50);
        }

        return $def;
    }
}

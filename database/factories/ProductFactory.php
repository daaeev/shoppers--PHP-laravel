<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Color;
use App\Models\Exchange;
use App\Models\Size;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => Str::random(),
            'subname' => $this->faker->sentence(5),
            'description' => $this->faker->text(),
            'currency' => Exchange::factory()->createOne()->currency_code,
            'price' => $this->faker->numberBetween(0, 100),
            'discount_price' => 0,
            'category_id' => Category::factory(),
            'color_id' => Color::factory(),
            'size_id' => Size::factory(),
            'count' => $this->faker->numberBetween(1, 10),
            'main_image' => 'test_main_image.png',
            'preview_image' => 'test_preview_image.png',
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'name' => $this->faker->unique()->sentence(5),
            'subname' => $this->faker->sentence(5),
            'description' => $this->faker->text(),
            'price' => $this->faker->numberBetween(0, 100),
            'discount_price' => 0,
            'category_id' => Category::factory(),
            'color_id' => Color::factory(),
            'size_id' => Size::factory(),
            'count' => $this->faker->numberBetween(0, 10),
            'main_image' => $this->faker->image(public_path('storage/products_images'), 640, 480, null, false),
            'preview_image' => $this->faker->image(public_path('storage/products_images'), 640, 480, null, false),
        ];
    }
}

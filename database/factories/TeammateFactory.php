<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Teammate>
 */
class TeammateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'full_name' => Str::random(30),
            'position' => Str::random(30),
            'description' => Str::random(255),
            'image' => $this->faker->image(public_path('storage/teammates_images'), 640, 480, null, false),
        ];
    }
}

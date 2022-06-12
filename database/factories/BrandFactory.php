<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory
 */
class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $name = $this->faker->word . rand(0, 100000);
        $slug = Str::slug($name);
        return [
            'name' => $name,
            'slug' => $slug,
            'meta_title' => $this->faker->sentence,
            'meta_description' => $this->faker->paragraph,
            'logo' => $this->faker->imageUrl,
            'top' => 0,
            'serial' => 1
        ];
    }
}

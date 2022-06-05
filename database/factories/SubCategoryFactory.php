<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory
 */
class SubCategoryFactory extends Factory
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
            'category_id' => 1,
            'name' => $name,
            'slug' => $slug,
            'meta_title' => $this->faker->sentence,
            'meta_description' => $this->faker->paragraph,
            'icon' => $this->faker->imageUrl,
            'banner' => $this->faker->imageUrl,
            'commission_rate' => $this->faker->numberBetween(0, 99),
            'featured' => $this->faker->boolean,
            'digital' => $this->faker->boolean
        ];
    }
}

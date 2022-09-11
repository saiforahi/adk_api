<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory
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
        $name = $this->faker->word . rand(0, 100000);
        $slug = Str::slug($name);
        return [
            'brand_id' => 1,
            'category_id' => 1,
            'sub_category_id' => 1,
            'sub_sub_category_id' => 1,
            'name' => $name,
            'slug' => $slug,
            'property_options' => $this->faker->text,
            'unit' => $this->faker->numberBetween(0, 100),
            'weight' => 10,
            'height' => 10,
            'length' => 10,
            'width' => 10,
            'mrp'=>100.02,
            'product_type' => 'product type',
            'colors' => 'red',
            'attributes' => 'attributes',
            'attribute_options' => 'attribute_options',
            'order_quantity_limit' => $this->faker->boolean,
            'order_quantity_max' => $this->faker->numberBetween(0, 100),
            'order_quantity_min' => $this->faker->numberBetween(0, 100),
            'price_type' => $this->faker->boolean,
            'unit_price' => $this->faker->randomFloat(2, 10, 100),
            'description' => $this->faker->text,
            'num_of_sale' => 0,
            'added_by_type' => 'faker',
            'digital' => $this->faker->boolean,
            'created_at' => now()
        ];
    }
}

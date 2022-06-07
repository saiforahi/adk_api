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
            'category_label' => $this->faker->boolean,
            'property_options' => $this->faker->text,
            'unit_type' => 'size',
            'unit_value' => 30,
            'unit' => $this->faker->numberBetween(0, 100),
            'weight' => 10,
            'length' => 10,
            'width' => 10,
            'tags' => 'demo tag',
            'product_type' => 'product type',
            'photos' => $this->faker->imageUrl,
            'thumbnail_img' => $this->faker->imageUrl,
            'featured_img' => $this->faker->imageUrl,
            'flash_deal_img' => $this->faker->imageUrl,
            'video_link' => $this->faker->imageUrl,
            'colors' => 'red',
            'color_image' => $this->faker->imageUrl,
            'color_type' => $this->faker->boolean,
            'attributes' => 'attributes',
            'attribute_options' => 'attribute_options',
            'tax' => $this->faker->numberBetween(0, 99),
            'tax_type' => 'tax_type',
            'discount' => $this->faker->randomFloat(2, 10, 100),
            'discount_type' => 'TYPE',
            'discount_variation' => $this->faker->boolean,
            'order_quantity_limit' => $this->faker->boolean,
            'order_quantity_max' => $this->faker->numberBetween(0, 100),
            'order_quantity_min' => $this->faker->numberBetween(0, 100),
            'price_type' => $this->faker->boolean,
            'stock_management' => $this->faker->boolean,
            'unit_price' => $this->faker->randomFloat(2, 10, 100),
            'sku' => $this->faker->shuffleString,
            'description' => $this->faker->text,
            'short_description' => $this->faker->text,
            'shipping_type' => $this->faker->boolean,
            'shipping_cost' => $this->faker->randomFloat(2, 10, 100),
            'num_of_sale' => 0,
            'meta_title' => $this->faker->word,
            'meta_description' => $this->faker->paragraph,
            'meta_img' => $this->faker->imageUrl,
            'rating' => $this->faker->randomFloat(0, 0, 5),
            'added_by' => 'faker',
            'digital' => $this->faker->boolean,
            'created_at' => now()
        ];
    }
}

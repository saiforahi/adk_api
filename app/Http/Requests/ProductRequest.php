<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'brand_id' => 'required',
            'category_id' => 'required',
            'sub_category_id' => 'nullable',
            'sub_sub_category_id' => 'nullable',
            'slug' => 'nullable',
            'category_label' => 'nullable',
            'property_options' => 'nullable',
            'unit_type' => 'nullable',
            'unit_value' => 'nullable',
            'unit' => 'nullable',
            'weight' => 'nullable',
            'length' => 'nullable',
            'width' => 'nullable',
            'tags' => 'nullable',
            'product_type' => 'nullable',
            'photos' => 'nullable',
            'thumbnail_img' => 'nullable',
            'featured_img' => 'nullable',
            'flash_deal_img' => 'nullable',
            'video_link' => 'nullable',
            'colors' => 'nullable',
            'color_image' => 'nullable',
            'color_type' => 'nullable',
            'attributes' => 'nullable',
            'attribute_options' => 'nullable',
            'tax' => 'nullable',
            'tax_type' => 'nullable',
            'discount' => 'nullable',
            'discount_type' => 'nullable',
            'discount_variation' => 'nullable',
            'order_quantity_limit' => 'nullable',
            'order_quantity_max' => 'nullable',
            'order_quantity_min' => 'nullable',
            'price_type' => 'nullable',
            'stock_management' => 'nullable',
            'unit_price' => 'nullable',
            'sku' => 'nullable',
            'description' => 'nullable',
            'short_description' => 'nullable',
            'shipping_type' => 'nullable',
            'shipping_cost' => 'nullable',
            'num_of_sale' => 'nullable',
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
            'meta_img' => 'nullable',
            'rating' => 'nullable',
            'added_by' => 'nullable',
            'digital' => 'nullable',
        ];
    }
}

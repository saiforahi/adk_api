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
            'brand_id' => [
                'nullable',
                'exists:brands,id'
            ],
            'category_id' => [
                'nullable',
                'exists:categories,id'
            ],
            'sub_category_id' => [
                'nullable',
                'exists:sub_categories,id'
            ],
            'sub_sub_category_id' => [
                'nullable',
                'exists:sub_sub_categories,id'
            ],
            'name' => 'required',
            'sort_desc' => 'nullable',
            'property_options' => 'nullable',
            'unit' => 'nullable',
            'weight' => 'nullable',
            'height' => 'nullable',
            'length' => 'nullable',
            'width' => 'nullable',
            'product_type' => 'nullable',
            'colors' => 'nullable',
            'attributes' => 'nullable',
            'attribute_options' => 'nullable',
            'order_quantity_limit' => 'nullable',
            'order_quantity_max' => 'nullable',
            'order_quantity_min' => 'nullable',
            'price_type' => 'nullable',
            'unit_price' => 'nullable',
            'currency_id' => 'nullable',
            'quantity' => 'nullable',
            'description' => 'nullable',
            'num_of_sale' => 'nullable',
            'digital' => 'nullable',
        ];
    }
}

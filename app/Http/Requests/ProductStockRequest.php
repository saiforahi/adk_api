<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductStockRequest extends FormRequest
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
            'product_id' => [
                'required',
                'exists:products,id',
                Rule::unique('product_stocks')
                    ->ignore($this->product_stock)
                    ->whereNull('deleted_at')
            ],
            'variant' => 'nullable',
            'sku' => 'nullable',
            'quantity' => 'required',
            'price' => 'required'
        ];
    }
}

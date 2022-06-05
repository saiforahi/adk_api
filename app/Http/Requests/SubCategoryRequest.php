<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property mixed $sub_category
 */
class SubCategoryRequest extends FormRequest
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
            'category_id' => [
                $this->sub_category ? 'nullable' : 'required',
                'exists:categories,id'
            ],
            'name' => [
                'required',
                Rule::unique('sub_categories')
                    ->ignore($this->sub_category)
                    ->whereNull('deleted_at')
            ],
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'featured' => 'nullable|boolean',
            'digital' => 'nullable|boolean',
            'commission_rate' => 'nullable'
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BrandRequest extends FormRequest
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
            'name' => [
                'required',
                Rule::unique('brands')
                    ->ignore($this->brand)
            ],
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'top' => 'nullable',
            'serial' => 'nullable'
        ];
    }
}

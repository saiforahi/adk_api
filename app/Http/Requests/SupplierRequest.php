<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
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
            //
            'company_name'=>'required|string|max:255',
//            'company_contact'=>'required|string|max:255',
            'first_name'=>'required|string|max:255',
            'last_name'=>'sometimes|nullable|string|max:255',
            'email'=>'required|email|unique:suppliers,email',
            'phone'=>'required|string|max:20|min:9|unique:suppliers,phone',
            'address'=>'sometimes|nullable',
            // 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'image' => 'nullable',
            'status' => 'boolean|nullable'
        ];
    }
}

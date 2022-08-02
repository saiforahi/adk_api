<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubPreRequest extends FormRequest
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
            'dealer_type_id'=>'required|exists:dealer_types,id',
            'username'=>'required|string|max:255|unique:dealers,username',
            'first_name'=>'required|string|max:255',
            'last_name'=>'sometimes|nullable|string|max:255',
            'email'=>'sometimes|nullable|email|max:255|unique:dealers,email',
            'phone'=>'required|string|max:20|min:9|unique:dealers,phone',
            'address'=>'sometimes|nullable|string',
            'password' => 'required|string|min:8',
            // 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            // 'image' => 'nullable',
        ];
    }
}

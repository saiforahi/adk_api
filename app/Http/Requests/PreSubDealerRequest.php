<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PreSubDealerRequest extends FormRequest
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
            'sub_dealer_type_id'=>'sometimes|nullable|exists:sub_dealer_types,id',
            'pre_dealer_type_id'=>'sometimes|nullable|exists:pre_dealer_types,id',
            // 'reference_id'=>'required|string|max:255|exists:pre_n_sub_dealers,id',
            'placement_id'=>'required|numeric',
            'opening_balance'=>'required|numeric',
            'username'=>'required|string|max:255|unique:pre_n_sub_dealers,username',
            'first_name'=>'required|string|max:255',
            'last_name'=>'sometimes|nullable|string|max:255',
            'email'=>'sometimes|nullable|email|max:255|unique:pre_n_sub_dealers,email',
            'phone'=>'required|string|max:20|min:9|unique:pre_n_sub_dealers,phone',
            'address'=>'sometimes|nullable|string',
            'password' => 'required|string|min:8'
        ];
    }
}

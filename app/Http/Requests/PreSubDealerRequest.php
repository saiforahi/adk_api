<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'reference_id'=>'required|string|max:255|exists:tycoons,id',
            'placement_id'=>'required|string|max:255|exists:tycoons,user_id',
            'opening_balance'=>'required|numeric',
            'username'=>'required|string|max:255|unique:,username',

            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tycoons')
                    ->ignore($this->dealer)
            ],

            'first_name'=>'required|string|max:255',
            'last_name'=>'sometimes|nullable|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('tycoons')
                    ->ignore($this->dealer)
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                'min:9',
                Rule::unique('tycoons')
                    ->ignore($this->dealer)
            ],
            'address'=>'sometimes|nullable|string',
            'password' => $this->dealer ? 'sometimes|nullable|min:8' : 'required|string|min:8'
        ];
    }
}

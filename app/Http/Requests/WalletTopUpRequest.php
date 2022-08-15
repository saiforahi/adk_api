<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WalletTopUpRequest extends FormRequest
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
            'type'=>'required|string|max:255',
            'mfs_acc_no'=>'sometimes|nullable|string|max:20',
            'mfs_trx_no'=>'sometimes|nullable|string|max:100',
            'bank_acc_no'=>'sometimes|nullable|string:max:100',
            'bank_branch'=>'sometimes|nullable|string|max:255',
            'bank_name'=>'sometimes|nullable|string|max:255',
            // 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'document' => 'nullable',
            'amount' => 'required|numeric'
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class PurchaseOrderRequest extends FormRequest
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
            'po_no' => [
                'required',
                'string',
                'max:255',
                Rule::unique('purchase_orders')
                    ->ignore($this->purchaseOrder)
            ],
            'purchase_date' => 'required',
            'supplier_id' => 'required',
            'warehouse_id' => 'required',
            'remarks' => 'nullable',
            'details.*' => 'required'
        ];
    }
}

<?php

namespace App\Modules\FbaAuto\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFbaAutoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipment_id'              => ['required', 'string', 'max:100', 'unique:fba_autos,shipment_id'],
            'shipment_date'            => ['required', 'date', 'date_format:Y-m-d'],
            'state'                    => ['required', 'string', 'max:100'],
            'warehouse_name'           => ['required', 'string', 'max:255'],
            'items'                    => ['required', 'array', 'min:1'],
            'items.*.product_name'     => ['required', 'string', 'max:255'],
            'items.*.qty'              => ['required', 'integer', 'min:1'],
            'items.*.qty_price'        => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'shipment_id.required'          => 'Shipment ID is required',
            'shipment_id.unique'            => 'This Shipment ID already exists',
            'shipment_date.required'        => 'Shipment date is required',
            'state.required'                => 'State is required',
            'warehouse_name.required'       => 'Warehouse name is required',
            'items.required'                => 'At least one product row is required',
            'items.min'                     => 'At least one product row is required',
            'items.*.product_name.required' => 'Product name is required',
            'items.*.qty.required'          => 'Quantity is required',
            'items.*.qty.min'               => 'Quantity must be at least 1',
            'items.*.qty_price.required'    => 'Total amount is required',
        ];
    }
}

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
            'shipment_date' => ['required', 'date', 'date_format:Y-m-d'],
            'shipment_time' => ['required', 'date_format:H:i'],
            'product_name' => ['required', 'string', 'max:255'],
            'qty' => ['required', 'integer', 'min:1'],
            'state' => ['required', 'string', 'max:100'],
            'warehouse_name' => ['required', 'string', 'max:255'],
            'qty_price' => ['required', 'numeric', 'min:0'],
            'status' => ['sometimes', Rule::in(['pending', 'processing', 'shipped', 'delivered', 'closed', 'cancelled', 'returned'])],
        ];
    }

    public function messages(): array
    {
        return [
            'shipment_date.required' => 'Shipment date is required',
            'shipment_time.required' => 'Shipment time is required',
            'product_name.required' => 'Product name is required',
            'qty.required' => 'Quantity is required',
            'qty.min' => 'Quantity must be at least 1',
            'state.required' => 'State is required',
            'warehouse_name.required' => 'Warehouse name is required',
            'qty_price.required' => 'Price is required',
        ];
    }
}

<?php

namespace App\Modules\FbaAuto\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFbaAutoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipment_date' => ['sometimes', 'required', 'date', 'date_format:Y-m-d'],
            'shipment_time' => ['sometimes', 'required', 'date_format:H:i'],
            'product_name' => ['sometimes', 'required', 'string', 'max:255'],
            'qty' => ['sometimes', 'required', 'integer', 'min:1'],
            'state' => ['sometimes', 'required', 'string', 'max:100'],
            'warehouse_name' => ['sometimes', 'required', 'string', 'max:255'],
            'qty_price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'status' => ['sometimes', Rule::in(['pending', 'processing', 'shipped', 'delivered', 'closed', 'cancelled', 'returned'])],
        ];
    }

    public function messages(): array
    {
        return [
            'qty.min' => 'Quantity must be at least 1',
            'qty_price.min' => 'Price must be a positive number',
        ];
    }
}

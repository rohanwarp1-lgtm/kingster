<?php

namespace App\Modules\Warranty\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarrantyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:20', 'regex:/^[0-9]{10,15}$/'],
            'email' => ['required', 'email', 'max:255'],
            'product_name' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'purchase_date' => ['required', 'date', 'before_or_equal:today'],
            'purchase_platform' => ['required', 'string', 'max:100'],
            'order_id' => ['required', 'string', 'max:255'],
            'warranty_type' => ['sometimes', 'in:standard,extended,premium'],
            'invoice_file' => ['sometimes', 'file', 'mimes:pdf,jpeg,jpg,png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.regex' => 'Mobile number must be between 10-15 digits',
            'purchase_date.before_or_equal' => 'Purchase date cannot be in the future',
            'invoice_file.max' => 'Invoice file must not exceed 5MB',
        ];
    }
}

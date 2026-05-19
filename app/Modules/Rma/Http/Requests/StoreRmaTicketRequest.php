<?php

namespace App\Modules\Rma\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRmaTicketRequest extends FormRequest
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
            'order_date' => ['required', 'date', 'before_or_equal:today'],
            'order_id' => ['required', 'string', 'max:255'],
            'bill_file' => ['sometimes', 'file', 'mimes:pdf,jpeg,jpg,png', 'max:5120'],
            'product_name' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'platform' => ['required', 'in:amazon,flipkart,other'],
            'issue_type' => ['required', 'in:hardware_defect,software_issue,missing_parts,wrong_item,damaged,other'],
            'issue_description' => ['required', 'string', 'max:5000'],
            'address' => ['required', 'string', 'max:1000'],
            'replacement_type' => ['required', 'in:full,partial,refund'],
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.regex' => 'Mobile number must be between 10-15 digits',
            'order_date.before_or_equal' => 'Order date cannot be in the future',
        ];
    }
}

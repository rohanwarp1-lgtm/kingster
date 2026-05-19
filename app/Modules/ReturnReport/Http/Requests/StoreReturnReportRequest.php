<?php

namespace App\Modules\ReturnReport\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReturnReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id' => ['required', 'string', 'max:255'],
            'product_name' => ['required', 'string', 'max:255'],
            'model_name' => ['required', 'string', 'max:255'],
            'marketplace' => ['required', 'in:amazon,flipkart,other'],
            'return_reason' => ['required', 'string', 'max:255'],
            'refund_status' => ['required', 'in:pending,processed,rejected,partial'],
            'return_cost' => ['required', 'numeric', 'min:0'],
            'loss_amount' => ['required', 'numeric', 'min:0'],
            'warehouse' => ['required', 'string', 'max:255'],
        ];
    }
}

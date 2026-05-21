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

    protected function prepareForValidation(): void
    {
        $items = collect($this->input('items', []))
            ->map(function ($item) {
                $item['product_name'] = $this->cleanText($item['product_name'] ?? '');
                return $item;
            })
            ->toArray();

        $this->merge([
            'state' => $this->cleanText($this->input('state', '')),
            'warehouse_name' => $this->cleanText($this->input('warehouse_name', '')),
            'items' => $items,
        ]);
    }

    public function rules(): array
    {
        return [
            'shipment_date'        => ['required', 'date', 'date_format:Y-m-d'],
            'state'                => ['required', 'string', 'max:100'],
            'warehouse_name'       => ['required', 'string', 'max:255'],
            'status'               => ['sometimes', Rule::in(['pending', 'processing', 'shipped', 'delivered', 'closed', 'cancelled', 'returned'])],
            'items'                => ['required', 'array', 'min:1'],
            'items.*.id'           => ['nullable', 'integer'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.qty'          => ['required', 'integer', 'min:1'],
            'items.*.qty_price'    => ['required', 'numeric', 'min:0', 'max:1000000000'],
        ];
    }

    public function messages(): array
    {
        return [
            'shipment_date.required'        => 'Shipment date is required',
            'state.required'                => 'State is required',
            'warehouse_name.required'       => 'Warehouse name is required',
            'items.required'                => 'At least one product row is required',
            'items.min'                     => 'At least one product row is required',
            'items.*.product_name.required' => 'Product name is required',
            'items.*.qty.required'          => 'Quantity is required',
            'items.*.qty.min'               => 'Quantity must be at least 1',
            'items.*.qty_price.required'    => 'Total amount is required',
            'items.*.qty_price.min'         => 'Price must be a positive number',
            'items.*.qty_price.max'         => 'Total amount cannot exceed ₹100 crore (₹1,00,00,00,000)',
        ];
    }

    private function cleanText(string $value): string
    {
        return trim(preg_replace('/\s+/', ' ', $value));
    }
}

<?php

namespace App\Modules\ReturnReport\Exports;

use App\Modules\ReturnReport\Models\ReturnReport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReturnReportExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private Collection $data
    ) {}

    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Order ID',
            'Product Name',
            'Model Name',
            'Marketplace',
            'Return Reason',
            'Refund Status',
            'Return Cost (₹)',
            'Loss Amount (₹)',
            'Warehouse',
            'Created At',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->order_id,
            $row->product_name,
            $row->model_name,
            ucfirst($row->marketplace),
            $row->return_reason,
            ucfirst(str_replace('_', ' ', $row->refund_status)),
            number_format($row->return_cost, 2),
            number_format($row->loss_amount, 2),
            $row->warehouse,
            $row->created_at->format('d M Y H:i'),
        ];
    }
}

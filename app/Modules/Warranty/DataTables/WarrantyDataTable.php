<?php

namespace App\Modules\Warranty\DataTables;

use App\Modules\Warranty\Models\WarrantyRegistration;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class WarrantyDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('ticket_no', function ($row) {
                return '<strong>' . $row->ticket_no . '</strong>';
            })
            ->addColumn('customer_name', function ($row) {
                return $row->customer_name . '<br><small class="text-muted">' . $row->email . '</small>';
            })
            ->addColumn('product', function ($row) {
                return $row->product_name . '<br><small class="text-muted">' . $row->model . '</small>';
            })
            ->addColumn('purchase_info', function ($row) {
                return '<small>' . $row->purchase_platform . '</small><br>' . 
                       '<strong>' . $row->formatted_price . '</strong><br>' .
                       '<small>' . $row->purchase_date->format('d M Y') . '</small>';
            })
            ->addColumn('warranty_type', function ($row) {
                $badges = [
                    'standard' => '<span class="badge bg-primary">Standard</span>',
                    'extended' => '<span class="badge bg-info">Extended</span>',
                    'premium' => '<span class="badge bg-success">Premium</span>',
                ];
                return $badges[$row->warranty_type] ?? '<span class="badge bg-secondary">Unknown</span>';
            })
            ->addColumn('expiry_date', function ($row) {
                $isExpired = $row->is_expired;
                $class = $isExpired ? 'text-danger' : ($row->expiry_date->diffInDays(now()) <= 30 ? 'text-warning' : 'text-success');
                return '<span class="' . $class . '">' . $row->expiry_date->format('d M Y') . '</span>';
            })
            ->addColumn('status', function ($row) {
                return $row->status_badge;
            })
            ->addColumn('actions', function ($row) {
                return view('warranty::partials.actions', ['row' => $row])->render();
            })
            ->rawColumns(['ticket_no', 'customer_name', 'product', 'purchase_info', 'warranty_type', 'expiry_date', 'status', 'actions']);
    }

    public function query(WarrantyRegistration $model): QueryBuilder
    {
        $filters = request()->all();
        
        return $model->with(['approver'])
            ->filter($filters)
            ->select('warranty_registrations.*')
            ->newQuery();
    }

    public function html(): Builder
    {
        return $this->builder()
            ->setTableId('warranty-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1, 'desc')
            ->buttons([
                ['extend' => 'excel', 'className' => 'btn btn-success btn-sm', 'text' => '<i class="bi bi-file-earmark-excel"></i> Excel'],
                ['extend' => 'csv', 'className' => 'btn btn-info btn-sm', 'text' => '<i class="bi bi-file-earmark-text"></i> CSV'],
                ['extend' => 'pdf', 'className' => 'btn btn-danger btn-sm', 'text' => '<i class="bi bi-file-earmark-pdf"></i> PDF'],
                ['extend' => 'print', 'className' => 'btn btn-secondary btn-sm', 'text' => '<i class="bi bi-printer"></i> Print'],
            ])
            ->parameters([
                'pageLength' => 25,
                'lengthMenu' => [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
                'processing' => true,
                'serverSide' => true,
                'ajax' => [
                    'url' => route('admin.warranty.ajax'),
                    'type' => 'GET',
                ],
                'language' => [
                    'processing' => '<span class="spinner-border spinner-border-sm"></span> Loading...',
                ],
            ]);
    }

    protected function getColumns(): array
    {
        return [
            ['data' => 'DT_RowIndex', 'title' => '#', 'orderable' => false, 'searchable' => false, 'width' => '5%'],
            ['data' => 'ticket_no', 'title' => 'Ticket No', 'width' => '12%'],
            ['data' => 'customer_name', 'title' => 'Customer', 'width' => '15%'],
            ['data' => 'product', 'title' => 'Product', 'width' => '15%'],
            ['data' => 'purchase_info', 'title' => 'Purchase Info', 'width' => '12%'],
            ['data' => 'warranty_type', 'title' => 'Type', 'width' => '8%'],
            ['data' => 'expiry_date', 'title' => 'Expires', 'width' => '10%'],
            ['data' => 'status', 'title' => 'Status', 'width' => '10%'],
            ['data' => 'actions', 'title' => 'Actions', 'orderable' => false, 'searchable' => false, 'width' => '13%'],
        ];
    }

    protected function filename(): string
    {
        return 'Warranty_Registrations_' . date('Ymd_His');
    }
}

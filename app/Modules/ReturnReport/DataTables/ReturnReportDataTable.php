<?php

namespace App\Modules\ReturnReport\DataTables;

use App\Modules\ReturnReport\Models\ReturnReport;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class ReturnReportDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('order_id', function ($row) {
                return '<strong>' . $row->order_id . '</strong>';
            })
            ->addColumn('product', function ($row) {
                return $row->product_name . '<br><small class="text-muted">' . $row->model_name . '</small>';
            })
            ->addColumn('marketplace', function ($row) {
                $badges = [
                    'amazon' => '<span class="badge bg-warning">Amazon</span>',
                    'flipkart' => '<span class="badge bg-primary">Flipkart</span>',
                    'other' => '<span class="badge bg-secondary">Other</span>',
                ];
                return $badges[$row->marketplace] ?? '<span class="badge bg-secondary">Unknown</span>';
            })
            ->addColumn('return_reason', function ($row) {
                return '<small>' . $row->return_reason . '</small>';
            })
            ->addColumn('refund_status', function ($row) {
                $badges = [
                    'pending' => '<span class="badge bg-warning">Pending</span>',
                    'processed' => '<span class="badge bg-success">Processed</span>',
                    'rejected' => '<span class="badge bg-danger">Rejected</span>',
                    'partial' => '<span class="badge bg-info">Partial</span>',
                ];
                return $badges[$row->refund_status] ?? '<span class="badge bg-secondary">Unknown</span>';
            })
            ->addColumn('return_cost', function ($row) {
                return '₹' . number_format($row->return_cost, 2);
            })
            ->addColumn('loss_amount', function ($row) {
                return '<strong class="text-danger">₹' . number_format($row->loss_amount, 2) . '</strong>';
            })
            ->addColumn('warehouse', function ($row) {
                return '<span class="badge bg-primary">' . $row->warehouse . '</span>';
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at->format('d M Y');
            })
            ->addColumn('actions', function ($row) {
                return view('admin.modules.return-report.partials.actions', ['row' => $row])->render();
            })
            ->rawColumns(['order_id', 'product', 'marketplace', 'return_reason', 'refund_status', 'loss_amount', 'warehouse', 'actions']);
    }

    public function query(ReturnReport $model): QueryBuilder
    {
        $filters = request()->all();
        
        return $model->filter($filters)
            ->select('return_reports.*')
            ->newQuery();
    }

    public function html(): Builder
    {
        return $this->builder()
            ->setTableId('return-report-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1, 'desc')
            ->buttons([
                ['extend' => 'excel', 'className' => 'btn btn-success btn-sm'],
                ['extend' => 'csv', 'className' => 'btn btn-info btn-sm'],
                ['extend' => 'pdf', 'className' => 'btn btn-danger btn-sm'],
                ['extend' => 'print', 'className' => 'btn btn-secondary btn-sm'],
            ])
            ->parameters([
                'pageLength' => 25,
                'processing' => true,
                'serverSide' => true,
                'ajax' => [
                    'url' => route('admin.return-report.ajax'),
                    'type' => 'GET',
                ],
            ]);
    }

    protected function getColumns(): array
    {
        return [
            ['data' => 'DT_RowIndex', 'title' => '#', 'orderable' => false, 'searchable' => false, 'width' => '5%'],
            ['data' => 'order_id', 'title' => 'Order ID', 'width' => '12%'],
            ['data' => 'product', 'title' => 'Product', 'width' => '15%'],
            ['data' => 'marketplace', 'title' => 'Marketplace', 'width' => '8%'],
            ['data' => 'return_reason', 'title' => 'Reason', 'width' => '12%'],
            ['data' => 'refund_status', 'title' => 'Refund', 'width' => '8%'],
            ['data' => 'return_cost', 'title' => 'Cost', 'width' => '8%'],
            ['data' => 'loss_amount', 'title' => 'Loss', 'width' => '8%'],
            ['data' => 'warehouse', 'title' => 'Warehouse', 'width' => '10%'],
            ['data' => 'created_at', 'title' => 'Date', 'width' => '8%'],
            ['data' => 'actions', 'title' => 'Actions', 'orderable' => false, 'searchable' => false, 'width' => '8%'],
        ];
    }

    protected function filename(): string
    {
        return 'Return_Report_' . date('Ymd_His');
    }
}

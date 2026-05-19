<?php

namespace App\Modules\FbaAuto\DataTables;

use App\Modules\FbaAuto\Models\FbaAuto;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Html\Column;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class FbaAutoDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="form-check-input row-checkbox" value="' . $row->id . '">';
            })
            ->addColumn('shipment_id', function ($row) {
                return '<strong>' . $row->shipment_id . '</strong>';
            })
            ->addColumn('product_name', function ($row) {
                return $row->product_name;
            })
            ->addColumn('warehouse', function ($row) {
                return '<span class="badge bg-primary">' . $row->warehouse_name . '</span>';
            })
            ->addColumn('state', function ($row) {
                return $row->state;
            })
            ->addColumn('qty', function ($row) {
                return '<strong>' . number_format($row->qty) . '</strong>';
            })
            ->addColumn('qty_price', function ($row) {
                return $row->formatted_price;
            })
            ->addColumn('status', function ($row) {
                return $row->status_badge;
            })
            ->addColumn('shipment_date', function ($row) {
                return $row->shipment_date->format('d M Y');
            })
            ->addColumn('generated_by', function ($row) {
                return $row->generator->name ?? 'N/A';
            })
            ->addColumn('actions', function ($row) {
                return view('fba-auto::partials.actions', ['row' => $row])->render();
            })
            ->rawColumns(['checkbox', 'shipment_id', 'product_name', 'warehouse', 'qty', 'status', 'actions']);
    }

    public function query(FbaAuto $model): QueryBuilder
    {
        $filters = request()->all();
        
        return $model->with(['generator', 'updater'])
            ->filter($filters)
            ->select('fba_autos.*')
            ->newQuery();
    }

    public function html(): Builder
    {
        return $this->builder()
            ->setTableId('fba-auto-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1, 'desc')
            ->buttons([
                ['extend' => 'excel', 'className' => 'btn btn-success btn-sm', 'text' => '<i class="bi bi-file-earmark-excel"></i> Excel'],
                ['extend' => 'csv', 'className' => 'btn btn-info btn-sm', 'text' => '<i class="bi bi-file-earmark-text"></i> CSV'],
                ['extend' => 'pdf', 'className' => 'btn btn-danger btn-sm', 'text' => '<i class="bi bi-file-earmark-pdf"></i> PDF'],
                ['extend' => 'print', 'className' => 'btn btn-secondary btn-sm', 'text' => '<i class="bi bi-printer"></i> Print'],
                ['extend' => 'colvis', 'className' => 'btn btn-dark btn-sm', 'text' => '<i class="bi bi-columns"></i> Columns'],
            ])
            ->parameters([
                'pageLength' => 25,
                'lengthMenu' => [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
                'processing' => true,
                'serverSide' => true,
                'ajax' => [
                    'url' => route('admin.fba-auto.ajax'),
                    'type' => 'GET',
                    'data' => function ($data) {
                        $data = array_merge($data, request()->only([
                            'warehouse', 'state', 'status', 'date_from', 'date_to', 'search'
                        ]));
                        return $data;
                    },
                ],
                'language' => [
                    'processing' => '<span class="spinner-border spinner-border-sm"></span> Loading...',
                    'emptyTable' => 'No shipments found',
                    'zeroRecords' => 'No matching shipments found',
                ],
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('checkbox')->title('<input type="checkbox" id="checkAll">')->orderable(false)->searchable(false)->width('3%'),
            Column::make('DT_RowIndex')->title('#')->orderable(false)->searchable(false)->width('5%'),
            Column::make('shipment_id')->title('Shipment ID')->width('12%'),
            Column::make('product_name')->title('Product Name')->width('15%'),
            Column::make('warehouse')->title('Warehouse')->width('10%'),
            Column::make('state')->title('State')->width('10%'),
            Column::make('qty')->title('Qty')->width('7%'),
            Column::make('qty_price')->title('Price')->width('10%'),
            Column::make('status')->title('Status')->width('10%'),
            Column::make('shipment_date')->title('Date')->width('10%'),
            Column::make('generated_by')->title('Created By')->width('10%'),
            Column::make('actions')->title('Actions')->orderable(false)->searchable(false)->width('13%'),
        ];
    }

    protected function filename(): string
    {
        return 'FBA_Auto_' . date('Ymd_His');
    }
}

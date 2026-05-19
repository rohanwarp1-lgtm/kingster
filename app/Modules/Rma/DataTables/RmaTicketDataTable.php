<?php

namespace App\Modules\Rma\DataTables;

use App\Modules\Rma\Models\RmaTicket;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class RmaTicketDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('ticket_id', function ($row) {
                return '<strong>' . $row->ticket_id . '</strong>';
            })
            ->addColumn('customer', function ($row) {
                return $row->customer_name . '<br><small class="text-muted">' . $row->email . '</small>';
            })
            ->addColumn('product', function ($row) {
                return $row->product_name . '<br><small class="text-muted">' . $row->model . '</small>';
            })
            ->addColumn('platform', function ($row) {
                $badges = [
                    'amazon' => '<span class="badge bg-warning">Amazon</span>',
                    'flipkart' => '<span class="badge bg-primary">Flipkart</span>',
                    'other' => '<span class="badge bg-secondary">Other</span>',
                ];
                return $badges[$row->platform] ?? '<span class="badge bg-secondary">Unknown</span>';
            })
            ->addColumn('issue_type', function ($row) {
                return '<small>' . ucfirst(str_replace('_', ' ', $row->issue_type)) . '</small>';
            })
            ->addColumn('assigned_to', function ($row) {
                return $row->assignee ? $row->assignee->name : '<span class="text-muted">Unassigned</span>';
            })
            ->addColumn('sla', function ($row) {
                return $row->sla_status . '<br><small>' . $row->sla_deadline->format('d M H:i') . '</small>';
            })
            ->addColumn('status', function ($row) {
                return $row->status_badge;
            })
            ->addColumn('actions', function ($row) {
                return view('rma::partials.actions', ['row' => $row])->render();
            })
            ->rawColumns(['ticket_id', 'customer', 'product', 'platform', 'issue_type', 'assigned_to', 'sla', 'status', 'actions']);
    }

    public function query(RmaTicket $model): QueryBuilder
    {
        $filters = request()->all();
        
        return $model->with(['assignee'])
            ->filter($filters)
            ->select('rma_tickets.*')
            ->newQuery();
    }

    public function html(): Builder
    {
        return $this->builder()
            ->setTableId('rma-table')
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
                    'url' => route('admin.rma.ajax'),
                    'type' => 'GET',
                ],
            ]);
    }

    protected function getColumns(): array
    {
        return [
            ['data' => 'DT_RowIndex', 'title' => '#', 'orderable' => false, 'searchable' => false, 'width' => '5%'],
            ['data' => 'ticket_id', 'title' => 'Ticket ID', 'width' => '12%'],
            ['data' => 'customer', 'title' => 'Customer', 'width' => '15%'],
            ['data' => 'product', 'title' => 'Product', 'width' => '15%'],
            ['data' => 'platform', 'title' => 'Platform', 'width' => '8%'],
            ['data' => 'issue_type', 'title' => 'Issue', 'width' => '10%'],
            ['data' => 'assigned_to', 'title' => 'Assigned', 'width' => '10%'],
            ['data' => 'sla', 'title' => 'SLA', 'width' => '10%'],
            ['data' => 'status', 'title' => 'Status', 'width' => '10%'],
            ['data' => 'actions', 'title' => 'Actions', 'orderable' => false, 'searchable' => false, 'width' => '13%'],
        ];
    }

    protected function filename(): string
    {
        return 'RMA_Tickets_' . date('Ymd_His');
    }
}

<?php $page = 'return-report-module'; ?>
@extends('layout.mainlayout_admin')
@section('title', 'Return Report Analytics')
@section('content')

<div class="page-wrapper">
    <div class="content container-fluid">
        
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Return Report Analytics</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.warranty.management') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Return Report</li>
                    </ul>
                </div>
                <div class="col-auto d-flex gap-2">
                    <button type="button" class="btn btn-primary" onclick="openCreateModal()">
                        <i class="fe fe-plus"></i> Add Return
                    </button>
                    <button type="button" class="btn btn-success" onclick="exportReport('excel')">
                        <i class="fe fe-download"></i> Export
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-2 stat-tiles mb-3">
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card stat-card stat-blue w-100">
                    <div class="card-body">
                        <div class="db-widgets">
                            <div class="db-info">
                                <h6>Total Returns</h6>
                                <h3 id="kpi-total">0</h3>
                            </div>
                            <div class="db-icon">
                                <i class="fe fe-refresh-cw"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card stat-card stat-red w-100">
                    <div class="card-body">
                        <div class="db-widgets">
                            <div class="db-info">
                                <h6>Total Loss</h6>
                                <h3 id="kpi-loss">₹0</h3>
                            </div>
                            <div class="db-icon">
                                <i class="fe fe-trending-down"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card stat-card stat-orange w-100">
                    <div class="card-body">
                        <div class="db-widgets">
                            <div class="db-info">
                                <h6>Avg Loss/Return</h6>
                                <h3 id="kpi-avg">₹0</h3>
                            </div>
                            <div class="db-icon">
                                <i class="fe fe-bar-chart-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card stat-card stat-purple w-100">
                    <div class="card-body">
                        <div class="db-widgets">
                            <div class="db-info">
                                <h6>Return Rate</h6>
                                <h3 id="kpi-rate">0%</h3>
                            </div>
                            <div class="db-icon">
                                <i class="fe fe-percent"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body py-2">
                <div class="row g-2 align-items-end">
                    <div class="col-auto">
                        <label class="form-label mb-1 small fw-semibold">Month</label>
                        <select id="filter-month" class="form-select form-select-sm" style="min-width:150px">
                            <option value="">All Months</option>
                            @foreach($months as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label mb-1 small fw-semibold">Marketplace</label>
                        <select id="filter-marketplace" class="form-select form-select-sm" style="min-width:150px">
                            <option value="">All Marketplaces</option>
                            @foreach($marketplaces as $mp)
                                <option value="{{ $mp }}">{{ $mp }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-primary" id="btn-apply-filter"><i class="fe fe-filter"></i> Filter</button>
                        <button type="button" class="btn btn-sm btn-secondary" id="btn-clear-filter"><i class="fe fe-x"></i> Clear</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Detailed Reports</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="return-report-table" class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order ID</th>
                                <th>Product</th>
                                <th>Model</th>
                                <th>Marketplace</th>
                                <th>Reason</th>
                                <th>Refund Status</th>
                                <th>Return Cost</th>
                                <th>Loss Amount</th>
                                <th>Warehouse</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.modules.return-report.partials.modals')
@endsection

@push('scripts')
<script>
function openCreateModal() {
    $('#createModal').modal('show');
}

$(document).ready(function() {
    loadDashboardData();

    var returnTable = $('#return-report-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('admin.return-report.ajax') }}',
            data: function (d) {
                d.month       = $('#filter-month').val();
                d.marketplace = $('#filter-marketplace').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'order_id', name: 'order_id'},
            {data: 'product_name', name: 'product_name'},
            {data: 'model_name', name: 'model_name'},
            {data: 'marketplace', name: 'marketplace'},
            {data: 'return_reason', name: 'return_reason'},
            {data: 'refund_status', name: 'refund_status'},
            {data: 'return_cost', name: 'return_cost'},
            {data: 'loss_amount', name: 'loss_amount'},
            {data: 'warehouse', name: 'warehouse'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        order: [[1, 'desc']]
    });

    $('#filter-month, #filter-marketplace').on('change', function () { returnTable.ajax.reload(); });
    $('#btn-apply-filter').on('click', function () { returnTable.ajax.reload(); });
    $('#btn-clear-filter').on('click', function () {
        $('#filter-month').val('');
        $('#filter-marketplace').val('');
        returnTable.ajax.reload();
    });
});

function loadDashboardData() {
    $.ajax({
        url: '{{ route('admin.return-report.dashboard') }}',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                let kpis = response.data.kpis;
                $('#kpi-total').text(kpis.total_returns);
                $('#kpi-loss').text('₹' + number_format(kpis.total_loss));
                $('#kpi-avg').text('₹' + number_format(kpis.avg_loss_per_return));
                $('#kpi-rate').text(kpis.return_rate + '%');
            }
        }
    });
}

function number_format(num) {
    return parseFloat(num).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

function exportReport(format) {
    window.location.href = `/admin/return-report/export?format=${format}`;
}

$('#create-form').on('submit', function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    
    $.ajax({
        url: '{{ route('admin.return-report.store') }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                $('#createModal').modal('hide');
                $('#create-form')[0].reset();
                returnTable.ajax.reload();
                loadDashboardData();
            }
        },
        error: function() {
            toastr.error('Error occurred');
        }
    });
});

$(document).on('click', '.delete-btn', function() {
    let id = $(this).data('id');
    Swal.fire({
        title: 'Delete this report?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/return-report/delete/${id}`,
                type: 'DELETE',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(response) {
                    if (response.success) {
                        toastr.success('Deleted successfully');
                        returnTable.ajax.reload();
                        loadDashboardData();
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        }
    });
});

$('#createModal').on('hidden.bs.modal', function() {
    $('#create-form')[0].reset();
});
</script>
@endpush

<?php $page = 'warranty-module'; ?>
@extends('layout.mainlayout_admin')
@section('title', 'Warranty Management')
@section('content')

<div class="page-wrapper">
    <div class="content container-fluid">
        
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Warranty Management</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.warranty.management') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Warranty</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-primary" onclick="openCreateModal()">
                        <i class="fe fe-plus"></i> New Registration
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-2 stat-tiles mb-3">
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card stat-card stat-purple w-100">
                    <div class="card-body">
                        <div class="db-widgets">
                            <div class="db-info">
                                <h6>Total Warranties</h6>
                                <h3>{{ $stats['total'] ?? 0 }}</h3>
                            </div>
                            <div class="db-icon">
                                <i class="fe fe-shield"></i>
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
                                <h6>Pending</h6>
                                <h3>{{ $stats['pending'] ?? 0 }}</h3>
                            </div>
                            <div class="db-icon">
                                <i class="fe fe-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card stat-card stat-green w-100">
                    <div class="card-body">
                        <div class="db-widgets">
                            <div class="db-info">
                                <h6>Approved</h6>
                                <h3>{{ $stats['approved'] ?? 0 }}</h3>
                            </div>
                            <div class="db-icon">
                                <i class="fe fe-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card stat-card stat-blue w-100">
                    <div class="card-body">
                        <div class="db-widgets">
                            <div class="db-info">
                                <h6>Expiring Soon</h6>
                                <h3>{{ $stats['expiring_soon'] ?? 0 }}</h3>
                            </div>
                            <div class="db-icon">
                                <i class="fe fe-calendar"></i>
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
                        <label class="form-label mb-1 small fw-semibold">Status</label>
                        <select id="filter-status" class="form-select form-select-sm" style="min-width:140px">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $s)
                                <option value="{{ $s }}">{{ ucwords(str_replace('_', ' ', $s)) }}</option>
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
                <h4 class="card-title">Warranty Registrations</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="warranty-table" class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ticket No</th>
                                <th>Customer</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>Product</th>
                                <th>Model</th>
                                <th>Serial</th>
                                <th>Platform</th>
                                <th>Purchase Date</th>
                                <th>Expiry Date</th>
                                <th>Status</th>
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

@include('admin.modules.warranty.partials.modals')
@endsection

@push('scripts')
<script>
function openCreateModal() {
    $('#createModal').modal('show');
}

$(function () {
    var warrantyTable = $('#warranty-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('admin.warranty.ajax') }}',
            data: function (d) {
                d.month  = $('#filter-month').val();
                d.status = $('#filter-status').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'ticket_no', name: 'ticket_no'},
            {data: 'customer_name', name: 'customer_name'},
            {data: 'mobile', name: 'mobile'},
            {data: 'email', name: 'email'},
            {data: 'product_name', name: 'product_name'},
            {data: 'model', name: 'model'},
            {data: 'serial_number', name: 'serial_number'},
            {data: 'purchase_platform', name: 'purchase_platform'},
            {data: 'purchase_date', name: 'purchase_date'},
            {data: 'expiry_date', name: 'expiry_date'},
            {data: 'status', name: 'status', orderable: false, searchable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        order: [[1, 'desc']]
    });

    $('#filter-month, #filter-status').on('change', function () { warrantyTable.ajax.reload(); });
    $('#btn-apply-filter').on('click', function () { warrantyTable.ajax.reload(); });
    $('#btn-clear-filter').on('click', function () {
        $('#filter-month').val('');
        $('#filter-status').val('');
        warrantyTable.ajax.reload();
    });
});

$(document).on('click', '.view-btn', function() {
    let id = $(this).data('id');
    window.location.href = `/admin/warranty/show/${id}`;
});

$(document).on('click', '.approve-btn', function() {
    let id = $(this).data('id');
    $('#warranty_id').val(id);
    $('#action_type').val('approve');
    $('#action-title').text('Approve Warranty');
    $('#actionModal').modal('show');
});

$(document).on('click', '.reject-btn', function() {
    let id = $(this).data('id');
    $('#warranty_id').val(id);
    $('#action_type').val('reject');
    $('#action-title').text('Reject Warranty');
    $('#actionModal').modal('show');
});

$('#action-form').on('submit', function(e) {
    e.preventDefault();
    let id = $('#warranty_id').val();
    let action = $('#action_type').val();
    let notes = $('#action_notes').val();
    
    let url = action === 'approve' 
        ? `/admin/warranty/approve/${id}` 
        : `/admin/warranty/reject/${id}`;
    
    $.ajax({
        url: url,
        type: 'POST',
        data: action === 'approve' ? {notes: notes} : {reason: notes},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(response) {
            toastr.success(response.message);
            $('#actionModal').modal('hide');
            warrantyTable.ajax.reload();
        },
        error: function() {
            toastr.error('Error occurred');
        }
    });
});

$('#create-form').on('submit', function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    
    $.ajax({
        url: '{{ route('admin.warranty.store') }}',
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
                warrantyTable.ajax.reload();
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
        title: 'Delete this warranty?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/warranty/delete/${id}`,
                type: 'DELETE',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(response) {
                    if (response.success) {
                        toastr.success('Deleted successfully');
                        warrantyTable.ajax.reload();
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        }
    });
});

$('#actionModal').on('hidden.bs.modal', function() {
    $('#action-form')[0].reset();
});
</script>
@endpush

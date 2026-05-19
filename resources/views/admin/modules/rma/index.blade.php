<?php $page = 'rma-module'; ?>
@extends('layout.mainlayout_admin')
@section('title', 'Customer Return')
@section('content')

<div class="page-wrapper">
    <div class="content container-fluid">
        
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Customer Return</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.warranty.management') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">RMA</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-primary" onclick="openCreateModal()">
                        <i class="fe fe-plus"></i> New Ticket
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card bg-comman w-100">
                    <div class="card-body">
                        <div class="db-widgets d-flex justify-content-between align-items-center">
                            <div class="db-info">
                                <h6>Total Tickets</h6>
                                <h3>{{ $stats['total'] ?? 0 }}</h3>
                            </div>
                            <div class="db-icon">
                                <i class="fe fe-ticket"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card bg-comman w-100">
                    <div class="card-body">
                        <div class="db-widgets d-flex justify-content-between align-items-center">
                            <div class="db-info">
                                <h6>Open</h6>
                                <h3>{{ $stats['open'] ?? 0 }}</h3>
                            </div>
                            <div class="db-icon">
                                <i class="fe fe-inbox"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card bg-comman w-100">
                    <div class="card-body">
                        <div class="db-widgets d-flex justify-content-between align-items-center">
                            <div class="db-info">
                                <h6>Overdue</h6>
                                <h3>{{ $stats['overdue'] ?? 0 }}</h3>
                            </div>
                            <div class="db-icon">
                                <i class="fe fe-alert-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card bg-comman w-100">
                    <div class="card-body">
                        <div class="db-widgets d-flex justify-content-between align-items-center">
                            <div class="db-info">
                                <h6>Closed</h6>
                                <h3>{{ $stats['closed'] ?? 0 }}</h3>
                            </div>
                            <div class="db-icon">
                                <i class="fe fe-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">RMA Tickets</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="rma-table" class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ticket ID</th>
                                <th>Customer</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>Order ID</th>
                                <th>Order Date</th>
                                <th>Product</th>
                                <th>Model</th>
                                <th>Platform</th>
                                <th>Issue Type</th>
                                <th>Status</th>
                                <th>SLA</th>
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

@include('admin.modules.rma.partials.modals')
@endsection

@push('scripts')
<script>
function openCreateModal() {
    $('#createModal').modal('show');
}

$(function () {
    $('#rma-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('admin.rma.ajax') }}',
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'ticket_id', name: 'ticket_id'},
            {data: 'customer_name', name: 'customer_name'},
            {data: 'mobile', name: 'mobile'},
            {data: 'email', name: 'email'},
            {data: 'order_id', name: 'order_id'},
            {data: 'order_date', name: 'order_date'},
            {data: 'product_name', name: 'product_name'},
            {data: 'model', name: 'model'},
            {data: 'platform', name: 'platform'},
            {data: 'issue_type', name: 'issue_type'},
            {data: 'status', name: 'status', orderable: false, searchable: false},
            {data: 'sla_deadline', name: 'sla_deadline'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        order: [[1, 'desc']]
    });
});

$(document).on('click', '.view-btn', function() {
    let id = $(this).data('id');
    window.location.href = `/admin/rma/show/${id}`;
});

$(document).on('click', '.status-btn', function() {
    let id = $(this).data('id');
    $('#ticket_id').val(id);
    $('#statusModal').modal('show');
});

$('#status-form').on('submit', function(e) {
    e.preventDefault();
    let id = $('#ticket_id').val();
    let newStatus = $('#new_status').val();
    let notes = $('#status_notes').val();
    
    $.ajax({
        url: `/admin/rma/update-status/${id}`,
        type: 'POST',
        data: {status: newStatus, notes: notes},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(response) {
            toastr.success(response.message);
            $('#statusModal').modal('hide');
            $('#rma-table').DataTable().ajax.reload();
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
        url: '{{ route('admin.rma.store') }}',
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
                $('#rma-table').DataTable().ajax.reload();
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
        title: 'Delete this ticket?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/rma/delete/${id}`,
                type: 'DELETE',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(response) {
                    if (response.success) {
                        toastr.success('Deleted successfully');
                        $('#rma-table').DataTable().ajax.reload();
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        }
    });
});

$('#statusModal, #createModal').on('hidden.bs.modal', function() {
    $(this).find('form')[0].reset();
});
</script>
@endpush

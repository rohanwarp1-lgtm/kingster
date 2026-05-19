<?php $page = 'fba-auto'; ?>
@extends('layout.mainlayout_admin')
@section('title', 'FBA Auto Management')
@section('content')

<div class="page-wrapper">
    <div class="content container-fluid">
        
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">FBA Auto Management</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.warranty.management') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">FBA Auto</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-primary" onclick="openCreateModal()">
                        <i class="fe fe-plus"></i> Add New Shipment
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
                                <h6>Total Shipments</h6>
                                <h3>{{ $stats['total'] ?? 0 }}</h3>
                            </div>
                            <div class="db-icon">
                                <i class="fe fe-box"></i>
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
                <div class="card bg-comman w-100">
                    <div class="card-body">
                        <div class="db-widgets d-flex justify-content-between align-items-center">
                            <div class="db-info">
                                <h6>Processing</h6>
                                <h3>{{ $stats['processing'] ?? 0 }}</h3>
                            </div>
                            <div class="db-icon">
                                <i class="fe fe-refresh-cw"></i>
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
                                <h6>Delivered</h6>
                                <h3>{{ $stats['delivered'] ?? 0 }}</h3>
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
                <h4 class="card-title">Shipments List</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="fba-auto-table" class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Shipment ID</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>State</th>
                                <th>Warehouse</th>
                                <th>Price</th>
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

@include('fba-auto::partials.modals')
@endsection

@push('scripts')
<script>
function openCreateModal() {
    $('#createModal').modal('show');
}

$(function () {
    $('#fba-auto-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('admin.fba-auto.ajax') }}',
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'shipment_id', name: 'shipment_id'},
            {data: 'shipment_date', name: 'shipment_date'},
            {data: 'shipment_time', name: 'shipment_time'},
            {data: 'product_name', name: 'product_name'},
            {data: 'qty', name: 'qty'},
            {data: 'state', name: 'state'},
            {data: 'warehouse_name', name: 'warehouse_name'},
            {data: 'qty_price', name: 'qty_price'},
            {data: 'status', name: 'status', orderable: false, searchable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        order: [[1, 'desc']]
    });
});

$(document).on('click', '.edit-btn', function() {
    let id = $(this).data('id');
    $.ajax({
        url: `/admin/fba-auto/edit/${id}`,
        type: 'GET',
        success: function(response) {
            $('#edit-content').html(response);
            $('#editModal').modal('show');
        },
        error: function() {
            toastr.error('Failed to load data');
        }
    });
});

$(document).on('click', '.delete-btn', function() {
    let id = $(this).data('id');
    Swal.fire({
        title: 'Are you sure?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/fba-auto/delete/${id}`,
                type: 'DELETE',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(response) {
                    if (response.success) {
                        toastr.success('Deleted successfully');
                        $('#fba-auto-table').DataTable().ajax.reload();
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        }
    });
});

$('#create-form').on('submit', function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    
    $.ajax({
        url: '{{ route('admin.fba-auto.store') }}',
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
                $('#fba-auto-table').DataTable().ajax.reload();
            } else {
                toastr.error(response.message);
            }
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON.message || 'Error occurred');
        }
    });
});

$('#edit-form').on('submit', function(e) {
    e.preventDefault();
    let id = $('#edit_id').val();
    let formData = new FormData(this);
    
    $.ajax({
        url: `/admin/fba-auto/update/${id}`,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                $('#editModal').modal('hide');
                $('#fba-auto-table').DataTable().ajax.reload();
            } else {
                toastr.error(response.message);
            }
        }
    });
});

$('#createModal, #editModal').on('hidden.bs.modal', function() {
    $(this).find('form')[0].reset();
});
</script>
@endpush

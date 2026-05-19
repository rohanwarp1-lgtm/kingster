@extends('layouts.admin')

@section('title', 'FBA Auto Management')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin_assets/css/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin_assets/css/buttons.dataTables.min.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-truck"></i> FBA Auto Management
        </h1>
        <div>
            <button type="button" class="btn btn-primary" onclick="openCreateModal()">
                <i class="bi bi-plus-circle"></i> Add New Shipment
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Shipments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-count">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-box-seam fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="pending-count">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Processing</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="processing-count">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-arrow-repeat fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Delivered</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="delivered-count">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 font-weight-bold text-primary">Shipments List</h6>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-sm btn-secondary" data-toggle="collapse" data-target="#filters">
                        <i class="bi bi-funnel"></i> Filters
                    </button>
                </div>
            </div>
            
            <div class="collapse mt-3" id="filters">
                <div class="row">
                    <div class="col-md-2">
                        <select name="warehouse" id="filter-warehouse" class="form-control form-control-sm">
                            <option value="">All Warehouses</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="state" id="filter-state" class="form-control form-control-sm">
                            <option value="">All States</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" id="filter-status" class="form-control form-control-sm">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="closed">Closed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="returned">Returned</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" id="filter-date-from" class="form-control form-control-sm" placeholder="Date From">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" id="filter-date-to" class="form-control form-control-sm" placeholder="Date To">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-primary" onclick="applyFilters()">
                            <i class="bi bi-search"></i> Apply
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="resetFilters()">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <div class="mb-3">
                <button type="button" class="btn btn-sm btn-danger" id="bulk-delete-btn" disabled onclick="bulkDelete()">
                    <i class="bi bi-trash"></i> Delete Selected
                </button>
                <button type="button" class="btn btn-sm btn-info" id="bulk-export-btn" disabled onclick="bulkExport()">
                    <i class="bi bi-download"></i> Export Selected
                </button>
            </div>
            
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table table-hover table-striped', 'id' => 'fba-auto-table']) !!}
            </div>
        </div>
    </div>
</div>

@include('fba-auto::partials.create-modal')
@include('fba-auto::partials.edit-modal')
@include('fba-auto::partials.status-modal')
@include('fba-auto::partials.view-modal')
@endsection

@push('scripts')
<script src="{{ asset('admin_assets/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin_assets/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('admin_assets/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('admin_assets/js/buttons.flash.min.js') }}"></script>
<script src="{{ asset('admin_assets/js/jszip.min.js') }}"></script>
<script src="{{ asset('admin_assets/js/pdfmake.min.js') }}"></script>
<script src="{{ asset('admin_assets/js/vfs_fonts.js') }}"></script>
<script src="{{ asset('admin_assets/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('admin_assets/js/buttons.print.min.js') }}"></script>
{!! $dataTable->scripts() !!}

<script>
$(document).ready(function() {
    loadStats();
    loadFilters();
});

function loadStats() {
    $.ajax({
        url: '{{ route('admin.fba-auto.stats') }}',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                $('#total-count').text(response.data.total);
                $('#pending-count').text(response.data.pending);
                $('#processing-count').text(response.data.processing);
                $('#delivered-count').text(response.data.delivered);
            }
        }
    });
}

function loadFilters() {
    $.ajax({
        url: '{{ route('admin.fba-auto.ajax') }}',
        type: 'GET',
        data: { only_filters: true },
        success: function(data) {
            let warehouses = data.warehouses || [];
            let states = data.states || [];
            
            let warehouseOptions = '<option value="">All Warehouses</option>';
            warehouses.forEach(w => {
                warehouseOptions += `<option value="${w}">${w}</option>`;
            });
            $('#filter-warehouse').html(warehouseOptions);
            
            let stateOptions = '<option value="">All States</option>';
            states.forEach(s => {
                stateOptions += `<option value="${s}">${s}</option>`;
            });
            $('#filter-state').html(stateOptions);
        }
    });
}

function applyFilters() {
    let warehouse = $('#filter-warehouse').val();
    let state = $('#filter-state').val();
    let status = $('#filter-status').val();
    let dateFrom = $('#filter-date-from').val();
    let dateTo = $('#filter-date-to').val();
    
    $('#fba-auto-table').DataTable().destroy();
    $('#fba-auto-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('admin.fba-auto.ajax') }}',
            type: 'GET',
            data: {
                warehouse: warehouse,
                state: state,
                status: status,
                date_from: dateFrom,
                date_to: dateTo,
            }
        },
        columns: [
            { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'shipment_id', name: 'shipment_id' },
            { data: 'product_name', name: 'product_name' },
            { data: 'warehouse', name: 'warehouse' },
            { data: 'state', name: 'state' },
            { data: 'qty', name: 'qty' },
            { data: 'qty_price', name: 'qty_price' },
            { data: 'status', name: 'status' },
            { data: 'shipment_date', name: 'shipment_date' },
            { data: 'generated_by', name: 'generated_by' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]
    });
}

function resetFilters() {
    $('#filter-warehouse, #filter-state, #filter-status, #filter-date-from, #filter-date-to').val('');
    applyFilters();
}

$(document).on('change', '.row-checkbox', function() {
    let checkedCount = $('.row-checkbox:checked').length;
    $('#bulk-delete-btn, #bulk-export-btn').prop('disabled', checkedCount === 0);
});

$('#checkAll').on('change', function() {
    $('.row-checkbox').prop('checked', $(this).prop('checked'));
    let checkedCount = $('.row-checkbox:checked').length;
    $('#bulk-delete-btn, #bulk-export-btn').prop('disabled', checkedCount === 0);
});

function openCreateModal() {
    $('#createModal').modal('show');
}

$(document).on('click', '.edit-btn', function() {
    let id = $(this).data('id');
    $.ajax({
        url: `/admin/fba-auto/edit/${id}`,
        type: 'GET',
        success: function(response) {
            $('#edit-form').html(response);
            $('#editModal').modal('show');
        },
        error: function() {
            toastr.error('Failed to load shipment data');
        }
    });
});

$(document).on('click', '.view-btn', function() {
    let id = $(this).data('id');
    $.ajax({
        url: `/admin/fba-auto/view/${id}`,
        type: 'GET',
        success: function(response) {
            $('#view-content').html(response);
            $('#viewModal').modal('show');
        }
    });
});

$(document).on('click', '.status-btn', function() {
    let id = $(this).data('id');
    let currentStatus = $(this).data('status');
    $('#status_id').val(id);
    $('#current_status').val(currentStatus);
    $('#statusModal').modal('show');
});

$(document).on('click', '.delete-btn', function() {
    let id = $(this).data('id');
    Swal.fire({
        title: 'Are you sure?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/fba-auto/delete/${id}`,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Shipment deleted successfully');
                        $('#fba-auto-table').DataTable().ajax.reload();
                        loadStats();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('Failed to delete shipment');
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
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                $('#createModal').modal('hide');
                $('#create-form')[0].reset();
                $('#fba-auto-table').DataTable().ajax.reload();
                loadStats();
            } else {
                toastr.error(response.message);
            }
        },
        error: function(xhr) {
            let errors = xhr.responseJSON.errors;
            displayValidationErrors(errors, 'create-form');
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
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                $('#editModal').modal('hide');
                $('#fba-auto-table').DataTable().ajax.reload();
                loadStats();
            } else {
                toastr.error(response.message);
            }
        },
        error: function(xhr) {
            let errors = xhr.responseJSON.errors;
            displayValidationErrors(errors, 'edit-form');
        }
    });
});

$('#status-form').on('submit', function(e) {
    e.preventDefault();
    let id = $('#status_id').val();
    let status = $('#new_status').val();
    let notes = $('#status_notes').val();
    
    $.ajax({
        url: `/admin/fba-auto/change-status/${id}`,
        type: 'POST',
        data: {
            status: status,
            notes: notes
        },
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                $('#statusModal').modal('hide');
                $('#status-form')[0].reset();
                $('#fba-auto-table').DataTable().ajax.reload();
                loadStats();
            } else {
                toastr.error(response.message);
            }
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON.message || 'Failed to update status');
        }
    });
});

function displayValidationErrors(errors, formId) {
    $(`#${formId} .invalid-feedback`).remove();
    $(`#${formId} .is-invalid`).removeClass('is-invalid');
    
    $.each(errors, function(field, messages) {
        let input = $(`#${formId} [name="${field}"]`);
        input.addClass('is-invalid');
        input.after(`<div class="invalid-feedback d-block">${messages[0]}</div>`);
    });
}

function bulkDelete() {
    let ids = [];
    $('.row-checkbox:checked').each(function() {
        ids.push($(this).val());
    });
    
    if (ids.length === 0) {
        toastr.warning('Please select at least one item');
        return;
    }
    
    Swal.fire({
        title: 'Are you sure?',
        text: `Delete ${ids.length} selected shipments?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete all!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route('admin.fba-auto.bulk-action') }}',
                type: 'POST',
                data: {
                    ids: ids,
                    action: 'delete'
                },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    if (response.success) {
                        toastr.success(`Deleted ${response.results.success.length} shipments`);
                        $('#fba-auto-table').DataTable().ajax.reload();
                        loadStats();
                        $('#checkAll').prop('checked', false);
                        $('#bulk-delete-btn, #bulk-export-btn').prop('disabled', true);
                    }
                },
                error: function() {
                    toastr.error('Bulk delete failed');
                }
            });
        }
    });
}

function bulkExport() {
    let ids = [];
    $('.row-checkbox:checked').each(function() {
        ids.push($(this).val());
    });
    
    if (ids.length === 0) {
        toastr.warning('Please select at least one item');
        return;
    }
    
    window.location.href = `/admin/fba-auto/export?ids=${ids.join(',')}&format=excel`;
}

$('#createModal').on('hidden.bs.modal', function() {
    $('#create-form')[0].reset();
    $(this).find('.invalid-feedback').remove();
    $(this).find('.is-invalid').removeClass('is-invalid');
});

$('#editModal').on('hidden.bs.modal', function() {
    $('#edit-form')[0].reset();
    $(this).find('.invalid-feedback').remove();
    $(this).find('.is-invalid').removeClass('is-invalid');
});

$('#statusModal').on('hidden.bs.modal', function() {
    $('#status-form')[0].reset();
});
</script>
@endpush

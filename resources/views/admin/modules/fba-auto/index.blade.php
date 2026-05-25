<?php $page = 'fba-auto'; ?>
@extends('layout.mainlayout_admin')
@section('title', 'FBA Shipment')
@section('content')

<style>
    #fba-auto-table td { vertical-align: top; }
    .fba-merged-line { min-height: 20px; padding: 1px 0; font-size: 13px; }
    .fba-merged-line + .fba-merged-line {
        border-top: 1px solid #edf0f4;
        margin-top: 2px;
        padding-top: 3px;
    }
    .fba-tags { margin-top: 2px; display: flex; flex-wrap: wrap; gap: 3px; }
    .fba-tag { font-size: 10px; background: #e9ecef; color: #495057; padding: 1px 5px; border-radius: 3px; }
    .fba-sub-text { font-size: 11px; color: #868e96; margin-top: 2px; }
    .fba-sub-text i { font-size: 10px; }
    .stat-tile-filter { cursor: pointer; transition: transform .15s ease, box-shadow .15s ease; }
    .stat-tile-filter:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,.15) !important; }
    .stat-tile-filter.active-filter { outline: 3px solid rgba(255,255,255,.7); transform: translateY(-1px); }
    .quick-date-btn.active { background: #0d6efd !important; color: #fff !important; border-color: #0d6efd !important; }
    #summary-card { display: none; }
    #report-section { display: none; }
    tfoot .grand-total td { font-weight: 700; background: #f8f9fa; border-top: 2px solid #dee2e6; }
</style>

<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">FBA Shipment</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.warranty.management') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">FBA Shipment</li>
                    </ul>
                </div>
                <div class="col-auto d-flex gap-2">
                    <button type="button" class="btn btn-outline-info btn-sm" id="btn-toggle-report">
                        <i class="fe fe-bar-chart-2"></i> Report
                    </button>
                    <button type="button" class="btn btn-success btn-sm" id="btn-export-excel">
                        <i class="fe fe-download"></i> Excel
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" id="btn-export-pdf">
                        <i class="fe fe-file-text"></i> PDF
                    </button>
                    <button type="button" class="btn btn-primary" onclick="openCreateModal()">
                        <i class="fe fe-plus"></i> Add New FBA Shipment
                    </button>
                </div>
            </div>
        </div>

        {{-- Stat Tiles (clickable filters) --}}
        <div class="row g-2 stat-tiles mb-3">
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card stat-card stat-purple w-100 stat-tile-filter" data-status="">
                    <div class="card-body">
                        <div class="db-widgets">
                            <div class="db-info">
                                <h6>Total Shipments</h6>
                                <h3>{{ $stats['total'] ?? 0 }}</h3>
                            </div>
                            <div class="db-icon"><i class="fe fe-box"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card stat-card stat-orange w-100 stat-tile-filter" data-status="pending">
                    <div class="card-body">
                        <div class="db-widgets">
                            <div class="db-info">
                                <h6>Pending</h6>
                                <h3>{{ $stats['pending'] ?? 0 }}</h3>
                            </div>
                            <div class="db-icon"><i class="fe fe-clock"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card stat-card stat-blue w-100 stat-tile-filter" data-status="processing">
                    <div class="card-body">
                        <div class="db-widgets">
                            <div class="db-info">
                                <h6>Processing</h6>
                                <h3>{{ $stats['processing'] ?? 0 }}</h3>
                            </div>
                            <div class="db-icon"><i class="fe fe-refresh-cw"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card stat-card stat-green w-100 stat-tile-filter" data-status="delivered">
                    <div class="card-body">
                        <div class="db-widgets">
                            <div class="db-info">
                                <h6>Delivered</h6>
                                <h3>{{ $stats['delivered'] ?? 0 }}</h3>
                            </div>
                            <div class="db-icon"><i class="fe fe-check-circle"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="card mb-3">
            <div class="card-body py-2">
                <div class="row g-2 align-items-end flex-wrap">
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
                        <label class="form-label mb-1 small fw-semibold">State</label>
                        <select id="filter-state" class="form-select form-select-sm" style="min-width:140px">
                            <option value="">All States</option>
                            @foreach($states as $state)
                                <option value="{{ $state }}">{{ $state }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label mb-1 small fw-semibold">Date</label>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary quick-date-btn active" data-date="">All</button>
                            <button type="button" class="btn btn-outline-secondary quick-date-btn" data-date="today">Today</button>
                            <button type="button" class="btn btn-outline-secondary quick-date-btn" data-date="yesterday">Yesterday</button>
                        </div>
                    </div>
                    <div class="col-auto d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-primary" id="btn-apply-filter">
                            <i class="fe fe-filter"></i> Filter
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" id="btn-clear-filter">
                            <i class="fe fe-x"></i> Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Summary Card --}}
        <div class="card mb-3" id="summary-card">
            <div class="card-body py-2">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <span class="fw-semibold small text-muted text-uppercase">Filter Summary:</span>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-primary fs-6 px-3 py-2">Total Qty: <strong id="sum-qty">0</strong></span>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-success fs-6 px-3 py-2">Total Amount: <strong id="sum-amount">₹0</strong></span>
                    </div>
                    <div class="col" id="state-breakdown"></div>
                </div>
            </div>
        </div>

        {{-- Report Section --}}
        <div id="report-section">
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header py-2"><h6 class="mb-0 fw-semibold">Overall</h6></div>
                        <div class="card-body" id="report-overall"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header py-2"><h6 class="mb-0 fw-semibold">By State</h6></div>
                        <div class="card-body p-0"><div class="table-responsive"><table class="table table-sm mb-0" id="report-state-table"><thead><tr><th>State</th><th>Shipments</th><th>Qty</th><th>Amount</th></tr></thead><tbody></tbody></table></div></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header py-2"><h6 class="mb-0 fw-semibold">By Product</h6></div>
                        <div class="card-body p-0"><div class="table-responsive"><table class="table table-sm mb-0" id="report-product-table"><thead><tr><th>Product</th><th>Qty</th><th>Amount</th></tr></thead><tbody></tbody></table></div></div>
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
                                <th>FBA Shipment ID</th>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>State</th>
                                <th>Warehouse</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr class="grand-total">
                                <td colspan="4" class="text-end fw-bold">Page Total:</td>
                                <td id="ft-qty" class="fw-bold"></td>
                                <td></td>
                                <td></td>
                                <td id="ft-amount" class="fw-bold"></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.modules.fba-auto.partials.modals')
@endsection

@push('scripts')
<script>
var productRowIndex = 0;
var activeStatusFilter = '';
var activeDateFilter = '';

function openCreateModal() {
    productRowIndex = 0;
    $('#product-rows').empty();
    addProductRow();
    initFbaSelect2($('#createModal'));
    $('#createModal').modal('show');
}

function addProductRow() {
    var idx = productRowIndex++;
    var row = `<tr id="row-${idx}">
        <td class="text-center row-num"></td>
        <td>
            <select name="items[${idx}][product_name]" class="form-select product-ajax-select2 w-100" style="width:100%" required>
                <option value=""></option>
            </select>
        </td>
        <td><input type="text" name="items[${idx}][asin]" class="form-control form-control-sm" placeholder="ASIN" maxlength="50"></td>
        <td><input type="text" name="items[${idx}][sku]" class="form-control form-control-sm" placeholder="SKU" maxlength="100"></td>
        <td><input type="text" name="items[${idx}][sku_label]" class="form-control form-control-sm" placeholder="SKU Label" maxlength="100"></td>
        <td><input type="number" name="items[${idx}][qty]" class="form-control form-control-sm" min="1" placeholder="0" required></td>
        <td><input type="number" name="items[${idx}][qty_price]" class="form-control form-control-sm" step="0.01" min="0" max="1000000000" placeholder="0.00" required></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn" data-row="${idx}">
                <i class="fe fe-trash-2"></i>
            </button>
        </td>
    </tr>`;
    $('#product-rows').append(row);
    initProductSelect2($(`#row-${idx} .product-ajax-select2`));
    updateRowNumbers();
}

function updateRowNumbers() {
    $('#product-rows tr').each(function(i) { $(this).find('.row-num').text(i + 1); });
}

var editProductRowIndex = 0;

function addEditProductRow() {
    var idx = 'n' + editProductRowIndex++;
    var row = `<tr>
        <td class="text-center row-num"></td>
        <td>
            <select name="items[${idx}][product_name]" class="form-select edit-product-select2 w-100" style="width:100%" required>
                <option value=""></option>
            </select>
        </td>
        <td><input type="text" name="items[${idx}][asin]" class="form-control form-control-sm" placeholder="ASIN" maxlength="50"></td>
        <td><input type="text" name="items[${idx}][sku]" class="form-control form-control-sm" placeholder="SKU" maxlength="100"></td>
        <td><input type="text" name="items[${idx}][sku_label]" class="form-control form-control-sm" placeholder="SKU Label" maxlength="100"></td>
        <td><input type="number" name="items[${idx}][qty]" class="form-control form-control-sm" min="1" placeholder="0" required></td>
        <td><input type="number" name="items[${idx}][qty_price]" class="form-control form-control-sm" step="0.01" min="0" max="1000000000" placeholder="0.00" required></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger remove-edit-row-btn">
                <i class="fe fe-trash-2"></i>
            </button>
        </td>
    </tr>`;
    $('#edit-product-rows').append(row);
    initProductSelect2($('#edit-product-rows tr:last .edit-product-select2'), $('#editModal'));
    updateEditRowNumbers();
}

function updateEditRowNumbers() {
    $('#edit-product-rows tr').each(function(i) { $(this).find('.row-num').text(i + 1); });
}

function initProductSelect2($el, $modal) {
    $modal = $modal || $('#createModal');
    $el.select2({
        width: '100%',
        placeholder: 'Search or type product name',
        allowClear: true,
        tags: true,
        dropdownParent: $modal,
        ajax: {
            url: '{{ route('admin.fba-auto.products.search') }}',
            dataType: 'json',
            delay: 150,
            data: function(params) { return { q: params.term || '' }; },
            processResults: function(data) { return data; },
            cache: false
        },
        minimumInputLength: 0,
        createTag: function(params) {
            var term = $.trim(params.term);
            if (!term) return null;
            return { id: term, text: term, isNew: true };
        },
        templateResult: function(data) {
            if (data.isNew) return $('<span><i class="fe fe-plus-circle me-1 text-primary"></i>Add: <strong>' + data.text + '</strong></span>');
            return data.text;
        }
    });
    $el.on('select2:open', function() {
        setTimeout(function() {
            var $s = $('.select2-container--open .select2-search__field');
            if ($s.length) $s[0].dispatchEvent(new Event('input', { bubbles: true }));
        }, 50);
    });
}

$(function () {
    initFbaSelect2($(document));

    var fbaTable = $('#fba-auto-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('admin.fba-auto.ajax') }}',
            data: function (d) {
                d.month         = $('#filter-month').val();
                d.state         = $('#filter-state').val();
                d.status_filter = activeStatusFilter;
                d.date_filter   = activeDateFilter;
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'shipment_id', name: 'shipment_id'},
            {data: 'shipment_date', name: 'shipment_date'},
            {data: 'product_name', name: 'product_name'},
            {data: 'qty', name: 'qty'},
            {data: 'state', name: 'state'},
            {data: 'warehouse_name', name: 'warehouse_name'},
            {data: 'qty_price', name: 'qty_price'},
            {data: 'status', name: 'status', orderable: false, searchable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false},
            {data: 'shipment_date_sort', name: 'shipment_date_sort', visible: false, searchable: false},
        ],
        columnDefs: [
            {targets: [2], orderData: [10]},
        ],
        order: [[2, 'desc']],
        footerCallback: function(row, data, start, end, display) {
            var api = this.api();
            var totalQty = 0, totalAmt = 0;
            api.rows({page: 'current'}).data().each(function(r) {
                r.qty_values.forEach(function(v) { totalQty += parseInt(v) || 0; });
                r.qty_price_values.forEach(function(v) { totalAmt += parseFloat(v) || 0; });
            });
            $('#ft-qty').text(totalQty.toLocaleString());
            $('#ft-amount').text('₹' + totalAmt.toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2}));
        }
    });

    // Store fbaTable globally so delete/edit handlers can reach it
    window.fbaTable = fbaTable;

    function reloadWithSummary() {
        fbaTable.ajax.reload(null, false);
        loadFilterSummary();
    }

    function loadFilterSummary() {
        $.get('{{ route('admin.fba-auto.filter-summary') }}', {
            month: $('#filter-month').val(),
            state: $('#filter-state').val(),
            status_filter: activeStatusFilter,
            date_filter: activeDateFilter,
        }, function(res) {
            if (!res.success) return;
            $('#sum-qty').text(res.total_qty.toLocaleString());
            $('#sum-amount').text('₹' + parseFloat(res.total_amount).toLocaleString('en-IN', {minimumFractionDigits:2}));
            var html = '';
            res.by_state.forEach(function(s) {
                html += '<span class="badge bg-secondary me-1 mb-1" style="font-size:11px">' +
                    e(s.state) + ': ' + parseInt(s.qty).toLocaleString() + ' qty / ₹' +
                    parseFloat(s.amount).toLocaleString('en-IN', {minimumFractionDigits:0, maximumFractionDigits:0}) + '</span>';
            });
            $('#state-breakdown').html(html);
            $('#summary-card').slideDown(200);
        });
    }

    function e(str) { return $('<div>').text(str).html(); }

    // Filters
    $('#filter-month, #filter-state').on('change', reloadWithSummary);
    $('#btn-apply-filter').on('click', reloadWithSummary);
    $('#btn-clear-filter').on('click', function() {
        $('#filter-month').val('');
        $('#filter-state').val('');
        activeDateFilter = '';
        activeStatusFilter = '';
        $('.quick-date-btn').removeClass('active');
        $('.quick-date-btn[data-date=""]').addClass('active');
        $('.stat-tile-filter').removeClass('active-filter');
        $('#summary-card').slideUp(200);
        fbaTable.ajax.reload(null, false);
    });

    // Quick date filter
    $(document).on('click', '.quick-date-btn', function() {
        activeDateFilter = $(this).data('date');
        $('.quick-date-btn').removeClass('active');
        $(this).addClass('active');
        reloadWithSummary();
    });

    // Stat tile click filters by status
    $(document).on('click', '.stat-tile-filter', function() {
        var status = $(this).data('status');
        if (activeStatusFilter === status) {
            activeStatusFilter = '';
            $(this).removeClass('active-filter');
        } else {
            activeStatusFilter = status;
            $('.stat-tile-filter').removeClass('active-filter');
            $(this).addClass('active-filter');
        }
        reloadWithSummary();
    });

    // Export Excel
    $('#btn-export-excel').on('click', function() {
        var params = new URLSearchParams({
            month: $('#filter-month').val(),
            state: $('#filter-state').val(),
            status_filter: activeStatusFilter,
            date_filter: activeDateFilter,
        });
        window.location.href = '{{ route('admin.fba-auto.export') }}?' + params.toString();
    });

    // Export PDF (print view)
    $('#btn-export-pdf').on('click', function() {
        window.print();
    });

    // Report toggle
    $('#btn-toggle-report').on('click', function() {
        if ($('#report-section').is(':visible')) {
            $('#report-section').slideUp(200);
            $(this).html('<i class="fe fe-bar-chart-2"></i> Report');
        } else {
            $('#report-section').slideDown(200);
            $(this).html('<i class="fe fe-x"></i> Close Report');
            loadReportData();
        }
    });

    function loadReportData() {
        $.get('{{ route('admin.fba-auto.report-data') }}', {
            month: $('#filter-month').val(),
            state: $('#filter-state').val(),
        }, function(res) {
            if (!res.success) return;
            var o = res.overall;
            $('#report-overall').html(
                '<p class="mb-1"><strong>Shipments:</strong> ' + o.total_shipments + '</p>' +
                '<p class="mb-1"><strong>Total Qty:</strong> ' + parseInt(o.total_qty).toLocaleString() + '</p>' +
                '<p class="mb-1"><strong>Revenue:</strong> ₹' + parseFloat(o.total_amount).toLocaleString('en-IN', {minimumFractionDigits:0, maximumFractionDigits:0}) + '</p>' +
                '<p class="mb-0"><strong>Avg/Shipment:</strong> ₹' + (o.total_shipments ? (o.total_amount/o.total_shipments).toLocaleString('en-IN',{minimumFractionDigits:0,maximumFractionDigits:0}) : 0) + '</p>'
            );

            var stateRows = '';
            res.by_state.forEach(function(s) {
                stateRows += '<tr><td>' + e(s.state) + '</td><td>' + s.shipments + '</td><td>' + parseInt(s.total_qty).toLocaleString() + '</td><td>₹' + parseFloat(s.total_amount).toLocaleString('en-IN',{minimumFractionDigits:0,maximumFractionDigits:0}) + '</td></tr>';
            });
            $('#report-state-table tbody').html(stateRows);

            var prodRows = '';
            res.by_product.forEach(function(p) {
                prodRows += '<tr><td>' + e(p.product_name) + '</td><td>' + parseInt(p.total_qty).toLocaleString() + '</td><td>₹' + parseFloat(p.total_amount).toLocaleString('en-IN',{minimumFractionDigits:0,maximumFractionDigits:0}) + '</td></tr>';
            });
            $('#report-product-table tbody').html(prodRows);
        });
    }
});

function initFbaSelect2($scope) {
    $scope.find('.fba-select2').each(function() {
        const $select = $(this);
        const $modal = $select.closest('.modal');
        if ($select.hasClass('select2-hidden-accessible')) $select.select2('destroy');
        $select.select2({
            width: '100%',
            tags: $select.data('tags') === true || $select.data('tags') === 1,
            placeholder: $select.data('placeholder') || 'Select option',
            allowClear: true,
            dropdownParent: $modal.length ? $modal : $(document.body),
            createTag: function(params) {
                var term = $.trim(params.term || '').replace(/\s+/g, ' ');
                if (!term) return null;
                return { id: term, text: term, isNew: true };
            },
            templateResult: function(data) {
                if (data.isNew) return $('<span><i class="fe fe-plus-circle me-1 text-primary"></i>Add: <strong>' + data.text + '</strong></span>');
                return data.text;
            }
        });
    });
}

$(document).on('click', '.edit-btn', function() {
    let id = $(this).data('id');
    $.ajax({
        url: `/admin/fba-auto/edit/${id}`,
        type: 'GET',
        success: function(response) {
            $('#edit-content').html(response);
            initFbaSelect2($('#editModal'));
            $('#editModal .edit-product-select2').each(function() {
                initProductSelect2($(this), $('#editModal'));
            });
            editProductRowIndex = $('#edit-product-rows tr').length;
            $('#editModal').modal('show');
        },
        error: function() { toastr.error('Failed to load data'); }
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
                        window.fbaTable.ajax.reload();
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        }
    });
});

$(document).on('click', '#add-product-row', function() { addProductRow(); });
$(document).on('click', '.remove-row-btn', function() {
    if ($('#product-rows tr').length === 1) { toastr.warning('At least one product row is required'); return; }
    $(this).closest('tr').remove();
    updateRowNumbers();
});

$('#create-form').on('submit', function(e) {
    e.preventDefault();
    $(this).find('select').trigger('change');
    var valid = true;
    $('#product-rows tr').each(function() {
        var product = $(this).find('select[name*="product_name"]').val();
        if (!product || product.trim() === '') {
            toastr.error('Please select or enter a product name for all rows');
            valid = false;
            return false;
        }
    });
    if (!valid) return;

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
                window.fbaTable.ajax.reload();
            } else {
                toastr.error(response.message);
            }
        },
        error: function(xhr) {
            let msg = xhr.responseJSON?.message || 'Error occurred';
            let errors = xhr.responseJSON?.errors;
            if (errors) msg = Object.values(errors).flat().join('<br>');
            toastr.error(msg);
        }
    });
});

var statusColors = {
    pending:'warning', processing:'info', shipped:'primary',
    delivered:'success', closed:'secondary', cancelled:'danger', returned:'dark'
};
var statusIcons = {
    pending:'fe-clock', processing:'fe-refresh-cw', shipped:'fe-truck',
    delivered:'fe-check-circle', closed:'fe-lock', cancelled:'fe-x-circle', returned:'fe-corner-down-left'
};

$(document).on('click', '.status-badge-btn', function() {
    var selected = $(this).data('status');
    $('#edit-status-input').val(selected);
    $('.status-badge-btn').each(function() {
        var s = $(this).data('status');
        $(this).removeClass('btn-' + statusColors[s]).addClass('btn-outline-' + statusColors[s]);
        $(this).html('<i class="fe ' + statusIcons[s] + ' me-1"></i>' + s.charAt(0).toUpperCase() + s.slice(1));
    });
    var color = statusColors[selected], icon = statusIcons[selected];
    $(this).removeClass('btn-outline-' + color).addClass('btn-' + color);
    $(this).html('<i class="fe ' + icon + ' me-1"></i>' + selected.charAt(0).toUpperCase() + selected.slice(1) + ' <i class="fe fe-check ms-1"></i>');
});

$(document).on('click', '#add-edit-product-row', function() { addEditProductRow(); });
$(document).on('click', '.remove-edit-row-btn', function() {
    if ($('#edit-product-rows tr').length === 1) { toastr.warning('At least one product row is required'); return; }
    $(this).closest('tr').remove();
    updateEditRowNumbers();
});

$(document).on('submit', '#edit-form', function(e) {
    e.preventDefault();
    $(this).find('select').trigger('change');
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
                window.fbaTable.ajax.reload();
            } else {
                toastr.error(response.message);
            }
        },
        error: function(xhr) {
            let msg = xhr.responseJSON?.message || 'Error occurred';
            let errors = xhr.responseJSON?.errors;
            if (errors) msg = Object.values(errors).flat().join('<br>');
            toastr.error(msg);
        }
    });
});

$('#createModal').on('hidden.bs.modal', function() {
    $('#create-form')[0].reset();
    $('#create-form .fba-select2').val(null).trigger('change');
    $('#product-rows').empty();
    productRowIndex = 0;
});

$('#editModal').on('hidden.bs.modal', function() {
    const form = $(this).find('form')[0];
    if (form) form.reset();
    $(this).find('.fba-select2').val(null).trigger('change');
});
</script>
@endpush

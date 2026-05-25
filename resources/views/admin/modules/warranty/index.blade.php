<?php $page = 'warranty-module'; ?>
@extends('layout.mainlayout_admin')
@section('title', 'Warranty Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css">
<style>
.nav-tabs-warranty .nav-link { font-size:13px; font-weight:600; color:#74788d; border-radius:8px 8px 0 0; padding:10px 22px; }
.nav-tabs-warranty .nav-link.active { color:#667eea; border-bottom-color:#fff; background:#fff; }
.nav-tabs-warranty .nav-link i { margin-right:6px; }
.mail-var-badge { display:inline-block; background:#f0f2ff; color:#667eea; border:1px solid #d0d4f7; border-radius:6px; padding:3px 10px; font-size:11.5px; font-weight:600; cursor:pointer; margin:3px 3px 3px 0; transition:all .15s; }
.mail-var-badge:hover { background:#667eea; color:#fff; }
.template-preview-wrap { border:1px solid #e0e3ef; border-radius:10px; overflow:hidden; }
.note-editor.note-frame { border-radius:8px; border:1px solid #e0e3ef; }
</style>
@endpush

@section('content')

<div class="page-wrapper">
    <div class="content container-fluid">

        {{-- Page Header --}}
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Warranty Management</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.warranty.management') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Warranty</li>
                    </ul>
                </div>
                <div class="col-auto" id="btn-new-reg-wrap">
                    <button type="button" class="btn btn-primary" onclick="openCreateModal()">
                        <i class="fe fe-plus"></i> New Registration
                    </button>
                </div>
            </div>
        </div>

        {{-- Stat Tiles --}}
        <div class="row g-2 stat-tiles mb-3">
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card stat-card stat-purple w-100">
                    <div class="card-body">
                        <div class="db-widgets">
                            <div class="db-info"><h6>Total Warranties</h6><h3>{{ $stats['total'] ?? 0 }}</h3></div>
                            <div class="db-icon"><i class="fe fe-shield"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card stat-card stat-orange w-100">
                    <div class="card-body">
                        <div class="db-widgets">
                            <div class="db-info"><h6>Pending</h6><h3>{{ $stats['pending'] ?? 0 }}</h3></div>
                            <div class="db-icon"><i class="fe fe-clock"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card stat-card stat-green w-100">
                    <div class="card-body">
                        <div class="db-widgets">
                            <div class="db-info"><h6>Approved</h6><h3>{{ $stats['approved'] ?? 0 }}</h3></div>
                            <div class="db-icon"><i class="fe fe-check-circle"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card stat-card stat-blue w-100">
                    <div class="card-body">
                        <div class="db-widgets">
                            <div class="db-info"><h6>Expiring Soon</h6><h3>{{ $stats['expiring_soon'] ?? 0 }}</h3></div>
                            <div class="db-icon"><i class="fe fe-calendar"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <ul class="nav nav-tabs nav-tabs-warranty mb-0" id="warrantyTabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab-registrations" id="tab-reg-link">
                    <i class="fe fe-shield"></i> Registrations
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-mail-template" id="tab-mail-link">
                    <i class="fe fe-mail"></i> Mail Template
                </a>
            </li>
        </ul>

        <div class="tab-content">

            {{-- ===== TAB 1 : REGISTRATIONS ===== --}}
            <div class="tab-pane fade show active" id="tab-registrations">

                {{-- Filters --}}
                <div class="card mb-3" style="border-radius:0 8px 8px 8px;">
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

                {{-- Table --}}
                <div class="card" style="border-radius:0 8px 8px 8px;">
                    <div class="card-header"><h4 class="card-title">Warranty Registrations</h4></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="warranty-table" class="table table-striped table-bordered w-100">
                                <thead>
                                    <tr>
                                        <th>#</th><th>Ticket No</th><th>Customer</th><th>Mobile</th>
                                        <th>Email</th><th>Product</th><th>Model</th><th>Serial</th>
                                        <th>Platform</th><th>Purchase Date</th><th>Expiry Date</th>
                                        <th>Status</th><th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>{{-- /tab-registrations --}}

            {{-- ===== TAB 2 : MAIL TEMPLATE ===== --}}
            <div class="tab-pane fade" id="tab-mail-template">
                <div class="card" style="border-radius:0 8px 8px 8px;">
                    <div class="card-header">
                        <h4 class="card-title mb-1">Warranty Email Templates</h4>
                        <small class="text-muted">Configure the emails sent to customers at each stage of their warranty.</small>
                    </div>
                    <div class="card-body">

                        {{-- Template Sub-Tabs --}}
                        <ul class="nav nav-pills mb-4 gap-1" id="tpl-pills">
                            <li class="nav-item">
                                <button class="nav-link active tpl-pill" data-type="warranty_registration"
                                        style="font-size:13px;">
                                    <i class="fe fe-file-text me-1"></i> Registration
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link tpl-pill" data-type="warranty_active"
                                        style="font-size:13px;color:#28c76f;">
                                    <i class="fe fe-check-circle me-1"></i> Active
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link tpl-pill" data-type="warranty_rejected"
                                        style="font-size:13px;color:#ea5455;">
                                    <i class="fe fe-x-circle me-1"></i> Rejected
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link tpl-pill" data-type="warranty_expired"
                                        style="font-size:13px;color:#74788d;">
                                    <i class="fe fe-clock me-1"></i> Expired
                                </button>
                            </li>
                        </ul>

                        {{-- Template description strip --}}
                        <div id="tpl-desc-strip" class="mb-3 p-2 px-3 rounded" style="background:#f8f9ff;border:1px solid #e8eaf6;font-size:13px;color:#74788d;"></div>

                        {{-- Variables helper --}}
                        <div class="mb-4 p-3" style="background:#f8f9ff;border-radius:10px;border:1px solid #e8eaf6;">
                            <p class="mb-2 fw-semibold text-muted" style="font-size:12px;letter-spacing:.5px;text-transform:uppercase;">
                                <i class="fe fe-tag me-1"></i> Available Variables — click to copy
                            </p>
                            <div>
                                @foreach(['{customer_name}'=>'Customer Name','{ticket_no}'=>'Ticket No','{product_name}'=>'Product','{model}'=>'Model','{serial_number}'=>'Serial','{purchase_date}'=>'Purchase Date','{expiry_date}'=>'Expiry Date','{warranty_type}'=>'Type','{reason}'=>'Reason'] as $var => $label)
                                <span class="mail-var-badge" onclick="copyVar('{{ $var }}')">{{ $var }} <small style="opacity:.7">{{ $label }}</small></span>
                                @endforeach
                            </div>
                        </div>

                        {{-- Template Form --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Subject</label>
                            <input type="text" id="tpl-subject" class="form-control"
                                placeholder="e.g. Your Warranty is Now Active – Ticket #{ticket_no}">
                            <small class="text-muted">Variables like <code>{ticket_no}</code> work here too.</small>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Email Body</label>
                            <textarea id="tpl-body" style="min-height:300px;"></textarea>
                            <small class="text-muted mt-1 d-block">
                                Logo, gradient header, and warranty details table are added automatically around this content.
                            </small>
                        </div>

                        <div class="d-flex gap-2 flex-wrap align-items-center">
                            <button type="button" class="btn btn-primary" id="btn-save-template">
                                <i class="fe fe-save me-1"></i> Save Template
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="btn-preview-template">
                                <i class="fe fe-eye me-1"></i> Preview
                            </button>
                            <div class="ms-auto d-flex gap-2 align-items-center">
                                <input type="email" id="test-email-input" class="form-control form-control-sm"
                                    placeholder="test@example.com" style="width:220px;" value="rsvadaliya54@gmail.com">
                                <button type="button" class="btn btn-sm btn-success" id="btn-send-test">
                                    <i class="fe fe-send me-1"></i> Send Test
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Preview Modal --}}
                <div class="modal fade" id="previewModal" tabindex="-1">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="fe fe-eye me-2"></i>Email Preview</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-0">
                                <iframe id="preview-iframe" style="width:100%;height:600px;border:none;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- /tab-mail-template --}}

        </div>{{-- /tab-content --}}

    </div>
</div>

@include('admin.modules.warranty.partials.modals')
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
/* ===== WARRANTY TABLE ===== */
function openCreateModal() { $('#createModal').modal('show'); }

var warrantyTable;
$(function () {
    warrantyTable = $('#warranty-table').DataTable({
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
            {data:'DT_RowIndex', name:'DT_RowIndex', orderable:false, searchable:false},
            {data:'ticket_no', name:'ticket_no'},
            {data:'customer_name', name:'customer_name'},
            {data:'mobile', name:'mobile'},
            {data:'email', name:'email'},
            {data:'product_name', name:'product_name'},
            {data:'model', name:'model'},
            {data:'serial_number', name:'serial_number'},
            {data:'purchase_platform', name:'purchase_platform'},
            {data:'purchase_date', name:'purchase_date'},
            {data:'expiry_date', name:'expiry_date'},
            {data:'status', name:'status', orderable:false, searchable:false},
            {data:'action', name:'action', orderable:false, searchable:false},
        ],
        order: [[1, 'desc']]
    });

    $('#filter-month, #filter-status').on('change', function () { warrantyTable.ajax.reload(); });
    $('#btn-apply-filter').on('click', function () { warrantyTable.ajax.reload(); });
    $('#btn-clear-filter').on('click', function () {
        $('#filter-month').val(''); $('#filter-status').val('');
        warrantyTable.ajax.reload();
    });
});

$(document).on('click', '.view-btn', function() {
    window.location.href = `/admin/warranty/show/${$(this).data('id')}`;
});
$(document).on('click', '.approve-btn', function() {
    $('#warranty_id').val($(this).data('id'));
    $('#action_type').val('approve');
    $('#action-title').text('Approve Warranty');
    $('#actionModal').modal('show');
});
$(document).on('click', '.reject-btn', function() {
    $('#warranty_id').val($(this).data('id'));
    $('#action_type').val('reject');
    $('#action-title').text('Reject Warranty');
    $('#actionModal').modal('show');
});
$('#action-form').on('submit', function(e) {
    e.preventDefault();
    let id = $('#warranty_id').val(), action = $('#action_type').val(), notes = $('#action_notes').val();
    let url = action === 'approve' ? `/admin/warranty/approve/${id}` : `/admin/warranty/reject/${id}`;
    $.ajax({
        url, type:'POST',
        data: action === 'approve' ? {notes} : {reason:notes},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(r) { toastr.success(r.message); $('#actionModal').modal('hide'); warrantyTable.ajax.reload(); },
        error: function()    { toastr.error('Error occurred'); }
    });
});
$('#create-form').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url:'{{ route('admin.warranty.store') }}', type:'POST',
        data: new FormData(this), processData:false, contentType:false,
        headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(r) {
            if (r.success) { toastr.success(r.message); $('#createModal').modal('hide'); $('#create-form')[0].reset(); warrantyTable.ajax.reload(); }
        },
        error: function() { toastr.error('Error occurred'); }
    });
});
$(document).on('click', '.delete-btn', function() {
    let id = $(this).data('id');
    Swal.fire({ title:'Delete this warranty?', icon:'warning', showCancelButton:true,
        confirmButtonColor:'#dc3545', cancelButtonColor:'#6c757d', confirmButtonText:'Yes, Delete!'
    }).then(r => {
        if (r.isConfirmed) {
            $.ajax({ url:`/admin/warranty/delete/${id}`, type:'DELETE',
                headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(res) {
                    if (res.success) { toastr.success('Deleted'); warrantyTable.ajax.reload(); }
                    else toastr.error(res.message);
                }
            });
        }
    });
});
$('#actionModal').on('hidden.bs.modal', function() { $('#action-form')[0].reset(); });

/* Show/hide New Registration button with active tab */
$('#tab-mail-link').on('shown.bs.tab', function() { $('#btn-new-reg-wrap').hide(); });
$('#tab-reg-link').on('shown.bs.tab', function()  { $('#btn-new-reg-wrap').show(); });

/* ===== STATUS CHANGE DROPDOWN ===== */
$(document).on('click', '.status-change-btn', function(e) {
    e.preventDefault();
    let id       = $(this).data('id');
    let status   = $(this).data('status');
    let labels   = {pending:'Pending', approved:'Active', rejected:'Rejected', expired:'Expired'};
    let colors   = {pending:'#ff9f43', approved:'#28c76f', rejected:'#ea5455', expired:'#74788d'};
    let isReject = (status === 'rejected');

    Swal.fire({
        title: 'Change Status to <span style="color:' + colors[status] + '">' + labels[status] + '</span>?',
        html: `<label class="form-label mt-2 mb-1 text-start d-block fw-semibold" style="font-size:13px;">
                   ${isReject ? 'Reason for Rejection <span style="color:#ea5455">*</span>' : 'Notes (optional)'}
               </label>
               <textarea id="swal-notes" class="form-control" rows="3"
                   placeholder="${isReject ? 'Enter the reason for rejecting this warranty...' : 'Add notes (optional)...'}"></textarea>
               ${isReject ? '<div id="swal-notes-err" class="text-danger mt-1" style="font-size:12px;display:none;">Reason is required to reject a warranty.</div>' : ''}`,
        icon: isReject ? 'warning' : 'question',
        showCancelButton: true,
        confirmButtonColor: colors[status],
        confirmButtonText: isReject ? 'Reject Warranty' : 'Yes, Change',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            let notes = document.getElementById('swal-notes').value.trim();
            if (isReject && !notes) {
                document.getElementById('swal-notes-err').style.display = 'block';
                return false;
            }
            return { notes };
        }
    }).then(result => {
        if (!result.isConfirmed) return;
        $.ajax({
            url: `/admin/warranty/change-status/${id}`,
            type: 'POST',
            data: { status, notes: result.value.notes, _token: $('meta[name="csrf-token"]').attr('content') },
            success: function(r) {
                if (r.success) { toastr.success(r.message); warrantyTable.ajax.reload(); }
                else toastr.error(r.message);
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors;
                let msg    = errors ? Object.values(errors).flat().join(' ') : (xhr.responseJSON?.message || 'Failed to change status');
                toastr.error(msg);
            }
        });
    });
});

/* ===== MAIL TEMPLATE ===== */
var currentTplType = 'warranty_registration';

var tplDescriptions = {
    warranty_registration: '📨 Sent when a customer submits a new warranty registration.',
    warranty_active:       '✅ Sent when admin marks a warranty as <strong>Active / Approved</strong>.',
    warranty_rejected:     '❌ Sent when admin <strong>rejects</strong> a warranty registration.',
    warranty_expired:      '⌛ Sent when admin marks a warranty as <strong>Expired</strong>.',
};

$(function () {
    $('#tpl-body').summernote({
        height: 320,
        toolbar: [
            ['style',  ['bold','italic','underline','clear']],
            ['para',   ['ul','ol','paragraph']],
            ['insert', ['link','hr']],
            ['view',   ['codeview','fullscreen']],
        ],
        placeholder: 'Write your email message here...',
        callbacks: { onInit: function() { loadTemplate('warranty_registration'); } }
    });

    $('.tpl-pill').on('click', function() {
        $('.tpl-pill').removeClass('active');
        $(this).addClass('active');
        currentTplType = $(this).data('type');
        loadTemplate(currentTplType);
    });
});

function loadTemplate(type) {
    $('#tpl-desc-strip').html(tplDescriptions[type] || '');
    $.get('{{ route('admin.warranty.mail-template.get') }}', {type}, function(r) {
        if (r.success && r.data) {
            $('#tpl-subject').val(r.data.subject);
            $('#tpl-body').summernote('code', r.data.body);
        } else {
            $('#tpl-subject').val('');
            $('#tpl-body').summernote('code', '');
        }
    });
}

$('#btn-save-template').on('click', function() {
    let subject = $('#tpl-subject').val().trim();
    let body    = $('#tpl-body').summernote('code');
    if (!subject) { toastr.warning('Please enter an email subject.'); return; }
    if (!body || body === '<p><br></p>') { toastr.warning('Please enter email body content.'); return; }

    let btn = $(this).text('Saving...').prop('disabled', true);
    $.ajax({
        url: '{{ route('admin.warranty.mail-template.save') }}',
        type: 'POST',
        data: { type: currentTplType, subject, body, _token: $('meta[name="csrf-token"]').attr('content') },
        success: function(r) { r.success ? toastr.success(r.message) : toastr.error(r.message); },
        error:   function()  { toastr.error('Failed to save template'); },
        complete:function()  { btn.html('<i class="fe fe-save me-1"></i> Save Template').prop('disabled', false); }
    });
});

$('#btn-preview-template').on('click', function() {
    let body    = $('#tpl-body').summernote('code');
    let subject = $('#tpl-subject').val();

    let preview = body
        .replace(/{customer_name}/g, 'John Doe')
        .replace(/{ticket_no}/g,     'WARR-TEST001')
        .replace(/{product_name}/g,  'Kingster Sample Product')
        .replace(/{model}/g,         'KG-2024-X')
        .replace(/{serial_number}/g, 'SN123456789')
        .replace(/{purchase_date}/g, '01 Jan 2025')
        .replace(/{expiry_date}/g,   '01 Jan 2026')
        .replace(/{warranty_type}/g, 'Standard')
        .replace(/{reason}/g,        'Documentation incomplete');

    let gradients = {
        warranty_registration: 'linear-gradient(135deg,#667eea 0%,#764ba2 100%)',
        warranty_active:       'linear-gradient(135deg,#28c76f 0%,#20a05a 100%)',
        warranty_rejected:     'linear-gradient(135deg,#ea5455 0%,#c0392b 100%)',
        warranty_expired:      'linear-gradient(135deg,#74788d 0%,#4a4e65 100%)',
    };
    let titles = {
        warranty_registration: 'Warranty Registration Received',
        warranty_active:       'Warranty Activated!',
        warranty_rejected:     'Warranty Not Approved',
        warranty_expired:      'Warranty Expired',
    };
    let badges = {
        warranty_registration: '<span style="background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;padding:5px 20px;border-radius:50px;font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;">&#9679; UNDER REVIEW</span>',
        warranty_active:       '<span style="background:#d4edda;color:#155724;padding:5px 20px;border-radius:50px;font-size:11px;font-weight:700;letter-spacing:1px;">&#9679; ACTIVATED</span>',
        warranty_rejected:     '<span style="background:#f8d7da;color:#721c24;padding:5px 20px;border-radius:50px;font-size:11px;font-weight:700;letter-spacing:1px;">&#9679; NOT APPROVED</span>',
        warranty_expired:      '<span style="background:#e2e3e5;color:#383d41;padding:5px 20px;border-radius:50px;font-size:11px;font-weight:700;letter-spacing:1px;">&#9679; EXPIRED</span>',
    };

    let grad  = gradients[currentTplType];
    let title = titles[currentTplType];
    let badge = badges[currentTplType];
    let logo  = '{{ asset('uploads/general_settings/kingster-white-logo.png') }}';

    let html = `<!DOCTYPE html><html><head><meta charset="UTF-8"><title>${subject}</title></head>
<body style="margin:0;padding:0;background:#f0f2f8;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f2f8;padding:30px 15px;"><tr><td align="center">
<table width="620" cellpadding="0" cellspacing="0" style="max-width:620px;width:100%;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 8px 40px rgba(0,0,0,.1);">
<tr><td style="background:${grad};padding:32px 40px;text-align:center;">
<img src="${logo}" height="44" alt="Kingster" style="height:44px;display:block;margin:0 auto;">
<h1 style="color:#fff;margin:18px 0 4px;font-size:20px;font-weight:700;">${title}</h1>
<p style="color:rgba(255,255,255,.8);margin:0;font-size:13px;">Ticket: <strong>WARR-TEST001</strong></p>
</td></tr>
<tr><td style="background:#f8f9ff;padding:12px 40px;text-align:center;border-bottom:1px solid #eef0f8;">${badge}</td></tr>
<tr><td style="padding:32px 40px 20px;color:#3d4166;font-size:15px;line-height:1.75;">${preview}</td></tr>
<tr><td style="padding:0 40px 30px;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9ff;border-radius:10px;border:1px solid #e8eaf6;overflow:hidden;">
<tr><td colspan="2" style="background:${grad};padding:10px 20px;"><p style="color:#fff;margin:0;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;">📄 Warranty Details</p></td></tr>
<tr><td style="padding:10px 20px;border-bottom:1px solid #e8eaf6;font-size:13px;color:#74788d;font-weight:500;width:45%;">Ticket Number</td><td style="padding:10px 20px;border-bottom:1px solid #e8eaf6;font-size:13px;color:#3d4166;font-weight:700;">WARR-TEST001</td></tr>
<tr style="background:#fff;"><td style="padding:10px 20px;border-bottom:1px solid #e8eaf6;font-size:13px;color:#74788d;font-weight:500;">Product</td><td style="padding:10px 20px;border-bottom:1px solid #e8eaf6;font-size:13px;color:#3d4166;">Kingster Sample Product</td></tr>
<tr><td style="padding:10px 20px;font-size:13px;color:#74788d;font-weight:500;">Expiry Date</td><td style="padding:10px 20px;font-size:13px;color:#3d4166;">01 Jan 2026</td></tr>
</table></td></tr>
<tr><td style="padding:0 40px 30px;text-align:center;">
<a href="#" style="display:inline-block;background:${grad};color:#fff;text-decoration:none;padding:13px 34px;border-radius:8px;font-size:14px;font-weight:600;">View Warranty Status</a>
</td></tr>
<tr><td style="padding:0 40px;"><hr style="border:none;border-top:1px solid #eef0f8;margin:0;"></td></tr>
<tr><td style="padding:24px 40px;text-align:center;">
<p style="margin:0 0 5px;font-size:13px;color:#74788d;">Need help? <a href="mailto:support@kingster.info" style="color:#667eea;">support@kingster.info</a></p>
<p style="margin:0;font-size:11px;color:#c0c4d6;">&copy; {{ date('Y') }} Kingster. All rights reserved.</p>
</td></tr>
</table></td></tr></table></body></html>`;

    document.getElementById('preview-iframe').srcdoc = html;
    $('#previewModal').modal('show');
});

$('#btn-send-test').on('click', function() {
    let email = $('#test-email-input').val().trim();
    if (!email) { toastr.warning('Enter a test email address'); return; }
    let btn = $(this).html('<i class="fe fe-loader me-1"></i>Sending...').prop('disabled', true);
    $.ajax({
        url: '{{ route('admin.warranty.mail-template.send-test') }}',
        type: 'POST',
        data: { email, type: currentTplType, _token: $('meta[name="csrf-token"]').attr('content') },
        success: function(r) { r.success ? toastr.success(r.message) : toastr.error(r.message); },
        error: function(xhr) { toastr.error(xhr.responseJSON?.message || 'Failed to send test mail'); },
        complete: function() { btn.html('<i class="fe fe-send me-1"></i> Send Test').prop('disabled', false); }
    });
});

function copyVar(v) {
    navigator.clipboard.writeText(v).then(() => toastr.info('Copied: ' + v, '', {timeOut:1500}));
}
</script>
@endpush

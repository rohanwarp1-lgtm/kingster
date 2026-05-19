<?php $page = 'warranty-module'; ?>
@extends('layout.mainlayout_admin')
@section('title', 'Warranty Details')
@section('content')

<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Warranty Details</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.warranty.management') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.warranty.index') }}">Warranty</a></li>
                        <li class="breadcrumb-item active">Details</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.warranty.index') }}" class="btn btn-secondary">
                        <i class="fe fe-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>

        @if (!$warranty)
            <div class="alert alert-warning">Warranty record not found.</div>
        @else
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h4 class="card-title mb-0">{{ $warranty->ticket_no }}</h4>
                                <div class="d-flex align-items-center gap-2">
                                    {!! $warranty->status_badge !!}
                                    @if(!in_array($warranty->status, ['approved', 'rejected', 'cancelled', 'expired']))
                                        <button type="button" class="btn btn-sm btn-success" onclick="submitWarrantyAction({{ $warranty->id }}, 'approve')">
                                            <i class="fe fe-check"></i> Approve
                                        </button>
                                        <button type="button" class="btn btn-sm btn-warning" onclick="submitWarrantyAction({{ $warranty->id }}, 'reject')">
                                            <i class="fe fe-x"></i> Reject
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="fw-bold">Customer</div>
                                    <div>{{ $warranty->customer_name ?? '-' }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="fw-bold">Contact</div>
                                    <div>{{ $warranty->mobile ?? '-' }}</div>
                                    <div>{{ $warranty->email ?? '-' }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="fw-bold">Product</div>
                                    <div>{{ $warranty->product_name ?? '-' }}</div>
                                    <div>Model: {{ $warranty->model ?? '-' }}</div>
                                    <div>Serial: {{ $warranty->serial_number ?? '-' }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="fw-bold">Purchase</div>
                                    <div>Order ID: {{ $warranty->order_id ?? '-' }}</div>
                                    <div>Platform: {{ $warranty->purchase_platform ?? '-' }}</div>
                                    <div>Date: {{ $warranty->purchase_date ? $warranty->purchase_date->format('d-M-Y') : '-' }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="fw-bold">Warranty</div>
                                    <div>Type: {{ ucfirst($warranty->warranty_type ?? 'standard') }}</div>
                                    <div>Expiry: {{ $warranty->expiry_date ? $warranty->expiry_date->format('d-M-Y') : '-' }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="fw-bold">Price</div>
                                    <div>{{ $warranty->formatted_price }}</div>
                                </div>
                                <div class="col-md-12">
                                    <div class="fw-bold">Approval Notes</div>
                                    <div style="white-space: pre-wrap;">{{ $warranty->approval_notes ?: '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Invoice</h4>
                        </div>
                        <div class="card-body">
                            @if($warranty->invoice_file)
                                <a href="{{ url($warranty->invoice_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fe fe-file"></i> View Invoice
                                </a>
                            @else
                                <div class="text-muted">No invoice uploaded.</div>
                            @endif
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Approval History</h4>
                        </div>
                        <div class="card-body">
                            @if(($warranty->approvals ?? collect())->isEmpty())
                                <div class="text-muted">No approval history.</div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>By</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($warranty->approvals as $approval)
                                                <tr>
                                                    <td>{!! $approval->action_badge !!}</td>
                                                    <td>{{ $approval->approver->username ?? 'System' }}</td>
                                                    <td>{{ $approval->created_at ? $approval->created_at->format('d-M-Y H:i') : '-' }}</td>
                                                </tr>
                                                @if($approval->notes)
                                                    <tr>
                                                        <td colspan="3" class="text-muted" style="white-space: pre-wrap;">{{ $approval->notes }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
@if($warranty)
<script>
function submitWarrantyAction(id, action) {
    Swal.fire({
        title: action === 'approve' ? 'Approve warranty?' : 'Reject warranty?',
        input: 'textarea',
        inputLabel: action === 'approve' ? 'Notes' : 'Reason',
        inputPlaceholder: action === 'approve' ? 'Optional notes' : 'Enter rejection reason',
        showCancelButton: true,
        confirmButtonText: action === 'approve' ? 'Approve' : 'Reject',
        confirmButtonColor: action === 'approve' ? '#28a745' : '#ffc107'
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
            url: `/admin/warranty/${action}/${id}`,
            type: 'POST',
            data: action === 'approve' ? {notes: result.value || ''} : {reason: result.value || 'Rejected by admin'},
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    window.location.reload();
                } else {
                    toastr.error(response.message || 'Action failed');
                }
            },
            error: function(xhr) {
                toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Action failed');
            }
        });
    });
}
</script>
@endif
@endpush

<?php $page = 'rma-module'; ?>
@extends('layout.mainlayout_admin')
@section('title', 'RMA Ticket Details')
@section('content')

<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">RMA Ticket Details</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.warranty.management') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.rma.index') }}">RMA</a></li>
                        <li class="breadcrumb-item active">Details</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.rma.index') }}" class="btn btn-secondary">
                        <i class="fe fe-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>

        @if (!$ticket)
            <div class="alert alert-warning">Ticket not found.</div>
        @else
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <h4 class="card-title mb-0">{{ $ticket->ticket_id }}</h4>
                                <div class="d-flex gap-2">
                                    {!! $ticket->status_badge !!}
                                    {!! $ticket->sla_status !!}
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="fw-bold">Customer</div>
                                    <div>{{ $ticket->customer_name ?? '-' }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="fw-bold">Contact</div>
                                    <div>{{ $ticket->mobile ?? '-' }}</div>
                                    <div>{{ $ticket->email ?? '-' }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="fw-bold">Order</div>
                                    <div>Order ID: {{ $ticket->order_id ?? '-' }}</div>
                                    <div>Order Date: {{ $ticket->order_date ? $ticket->order_date->format('d-M-Y') : '-' }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="fw-bold">Product</div>
                                    <div>{{ $ticket->product_name ?? '-' }}</div>
                                    <div>Model: {{ $ticket->model ?? '-' }}</div>
                                    <div>Platform: {{ $ticket->platform ?? '-' }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="fw-bold">Issue</div>
                                    <div>Type: {{ $ticket->issue_type ?? '-' }}</div>
                                    <div>{{ $ticket->issue_description ?? '-' }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="fw-bold">Replacement</div>
                                    <div>{{ $ticket->replacement_type ?? '-' }}</div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="fw-bold">Address</div>
                                    <div>{{ $ticket->address ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Comments</h4>
                        </div>
                        <div class="card-body">
                            @if (($ticket->comments ?? collect())->isEmpty())
                                <div class="text-muted">No comments.</div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>User</th>
                                                <th>Comment</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($ticket->comments as $comment)
                                                <tr>
                                                    <td>{{ $comment->type_label }}</td>
                                                    <td>{{ $comment->user->username ?? 'System' }}</td>
                                                    <td style="white-space: pre-wrap;">{{ $comment->content }}</td>
                                                    <td>{{ $comment->created_at ? $comment->created_at->format('d-M-Y H:i') : '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Assignment</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-2"><span class="fw-bold">Assigned To:</span> {{ $ticket->assignee->username ?? '-' }}</div>
                            <div><span class="fw-bold">SLA Deadline:</span> {{ $ticket->sla_deadline ? $ticket->sla_deadline->format('d-M-Y H:i') : '-' }}</div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Attachments</h4>
                        </div>
                        <div class="card-body">
                            @if (($ticket->attachments ?? collect())->isEmpty() && empty($ticket->bill_file))
                                <div class="text-muted">No attachments.</div>
                            @else
                                @if (!empty($ticket->bill_file))
                                    <div class="mb-2">
                                        <a href="{{ url($ticket->bill_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fe fe-file"></i> Bill File
                                        </a>
                                    </div>
                                @endif

                                @foreach ($ticket->attachments as $attachment)
                                    <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2">
                                        <div class="me-2">
                                            <div class="fw-bold">{{ $attachment->file_name }}</div>
                                            <div class="text-muted">{{ $attachment->formatted_size }}</div>
                                        </div>
                                        <a href="{{ url($attachment->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                            View
                                        </a>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@endsection


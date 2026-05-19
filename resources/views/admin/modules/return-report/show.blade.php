<?php $page = 'return-report-module'; ?>
@extends('layout.mainlayout_admin')
@section('title', 'Return Report Details')
@section('content')

<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Return Report Details</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.warranty.management') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.return-report.index') }}">Return Report</a></li>
                        <li class="breadcrumb-item active">Details</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.return-report.index') }}" class="btn btn-secondary">
                        <i class="fe fe-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>

        @if(!$report)
            <div class="alert alert-warning">Return report not found.</div>
        @else
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Order {{ $report->order_id }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="fw-bold">Product</div>
                            <div>{{ $report->product_name ?? '-' }}</div>
                            <div>Model: {{ $report->model_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="fw-bold">Marketplace</div>
                            <div>{{ ucfirst($report->marketplace ?? '-') }}</div>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="fw-bold">Warehouse</div>
                            <div>{{ $report->warehouse ?? '-' }}</div>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="fw-bold">Return Reason</div>
                            <div>{{ $report->return_reason ?? '-' }}</div>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="fw-bold">Refund Status</div>
                            <div>{{ ucfirst($report->refund_status ?? '-') }}</div>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="fw-bold">Created</div>
                            <div>{{ $report->created_at ? $report->created_at->format('d-M-Y H:i') : '-' }}</div>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="fw-bold">Return Cost</div>
                            <div>₹{{ number_format($report->return_cost, 2) }}</div>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="fw-bold">Loss Amount</div>
                            <div>₹{{ number_format($report->loss_amount, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@endsection

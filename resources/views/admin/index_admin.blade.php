<?php $page = 'warranty'; ?>
@extends('layout.mainlayout_admin')
@section('content')

<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Dashboard</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.warranty.management') }}">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Stat Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card stat-purple">
                    <div class="card-body">
                        <div class="db-widgets d-flex justify-content-between align-items-center">
                            <div class="db-info">
                                <h6>Total Warranties</h6>
                                <h3>{{ $stats['total_warranties'] }}</h3>
                            </div>
                            <div class="db-icon">
                                <i class="fe fe-file-text"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card stat-green">
                    <div class="card-body">
                        <div class="db-widgets d-flex justify-content-between align-items-center">
                            <div class="db-info">
                                <h6>Active Warranties</h6>
                                <h3>{{ $stats['active_warranties'] }}</h3>
                            </div>
                            <div class="db-icon">
                                <i class="fe fe-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card stat-orange">
                    <div class="card-body">
                        <div class="db-widgets d-flex justify-content-between align-items-center">
                            <div class="db-info">
                                <h6>Pending Warranties</h6>
                                <h3>{{ $stats['pending_warranties'] }}</h3>
                            </div>
                            <div class="db-icon">
                                <i class="fe fe-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card stat-blue">
                    <div class="card-body">
                        <div class="db-widgets d-flex justify-content-between align-items-center">
                            <div class="db-info">
                                <h6>Total Products</h6>
                                <h3>{{ $stats['total_products'] }}</h3>
                            </div>
                            <div class="db-icon">
                                <i class="fe fe-package"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Warranty Table --}}
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h4 class="card-title">Warranty Records</h4>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <select class="form-select form-select-sm" name="warranty_status" id="warranty_status" style="width:150px;">
                                <option value="All">All Status</option>
                                <option value="Pending">Pending</option>
                                <option value="Active">Active</option>
                                <option value="Expired">Expired</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                            <select class="form-select form-select-sm" name="status_filter" id="status_filter" style="width:140px;">
                                <option value="0">Active Records</option>
                                <option value="1">Deleted</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>Status</th>
                                        <th>Buyer Name</th>
                                        <th>Mobile</th>
                                        <th>Product</th>
                                        <th>Serial Number</th>
                                        <th>Purchase Date</th>
                                        <th>Expiry Date</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            @csrf
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

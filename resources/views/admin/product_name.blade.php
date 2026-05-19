<?php $page = 'product_name'; ?>
@extends('layout.mainlayout_admin')
@section('content')

<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Product Names</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.warranty.management') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Product Names</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Add / Edit Form --}}
        <div class="row mb-4">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title" id="productNameFormTitle">Add Product Name</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('product.name.store') }}" method="POST" id="productNameForm" novalidate>
                            @csrf
                            <input type="hidden" id="product_name_id" name="product_name_id" value="">
                            <div class="row align-items-end g-3">
                                <div class="col-md-9">
                                    <label for="product_name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="product_name" name="product_name"
                                           placeholder="Enter product name" autocomplete="off">
                                </div>
                                <div class="col-md-3 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-grow-1" id="productNameSaveBtn">
                                        <i class="fe fe-plus"></i> Save Name
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="productNameCancelBtn" style="display:none;">
                                        <i class="fe fe-x"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Names Table --}}
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Product Names List</h4>
                        <select class="form-select form-select-sm" id="products_name_status_filter" style="width:150px;">
                            <option value="0">Active</option>
                            <option value="1">Deleted</option>
                        </select>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable_3" class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>Name</th>
                                        <th>Created By</th>
                                        <th>Modified By</th>
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

@section('prouctpage-js')
<script>
$(function() {
    // Override productNameEdit to update form title and show cancel button
    window.productNameEditExtended = function(id, name) {
        $('#product_name_id').val(id);
        $('#product_name').val(name);
        $('#productNameFormTitle').text('Edit Product Name');
        $('#productNameSaveBtn').html('<i class="fe fe-save"></i> Update Name');
        $('#productNameCancelBtn').show();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    $('#productNameCancelBtn').on('click', function() {
        $('#product_name_id').val('');
        $('#product_name').val('');
        $('#productNameFormTitle').text('Add Product Name');
        $('#productNameSaveBtn').html('<i class="fe fe-plus"></i> Save Name');
        $(this).hide();
    });
});
</script>
@endsection

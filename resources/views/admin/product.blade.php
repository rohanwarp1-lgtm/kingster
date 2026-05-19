<?php $page = 'product'; ?>
@extends('layout.mainlayout_admin')
@section('content')

<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Product Management</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.warranty.management') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Products</li>
                    </ul>
                </div>
                <div class="col-auto d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary" id="product_indexing">
                        <i class="fe fe-list"></i> Reorder
                    </button>
                    <a href="{{ route('create.product.view') }}" class="btn btn-primary">
                        <i class="fe fe-plus"></i> Add Product
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Products List</h4>
                        <select class="form-select form-select-sm" id="products_management_status_filter" style="width:150px;">
                            <option value="0">Active</option>
                            <option value="1">Deleted</option>
                        </select>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable_1" class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>Product Name</th>
                                        <th>Offer Price</th>
                                        <th>Original Price</th>
                                        <th>Rating</th>
                                        <th>Reviews</th>
                                        <th>Sold</th>
                                        <th>Created By</th>
                                        <th>Modified By</th>
                                        <th>Created</th>
                                        <th>Updated</th>
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

{{-- Product Indexing Modal --}}
<div class="modal fade" id="indexingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Product Ordering</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="indexingForm" method="POST" action="{{ route('admin.product.updateIndexing') }}">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-3">Drag and drop to reorder products. The order here determines display order on the frontend.</p>
                    <ul id="sortable-products" class="list-group mb-3" style="min-height:50px;">
                        @foreach($products as $product)
                            <li class="list-group-item d-flex align-items-center gap-2" data-id="{{ $product->id }}" style="cursor:move; border-radius:8px; margin-bottom:4px;">
                                <i class="fe fe-move text-muted"></i>
                                <input type="checkbox" checked class="d-none form-check-input" name="product_indexes[]" value="{{ $product->id }}">
                                <span>{{ $product->product_name }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn gradientBTN">
                        <i class="fe fe-save"></i> Save Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('prouctpage-js')
<script>
$(function() {
    let sortableInitialized = false;

    $('#indexingModal').on('shown.bs.modal', function() {
        if (!sortableInitialized) {
            $("#sortable-products").sortable({ placeholder: "ui-state-highlight" });
            sortableInitialized = true;
        }
    });

    $('#indexingForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function() {
                toastr.success('Product order saved!');
                var modal = bootstrap.Modal.getInstance(document.getElementById('indexingModal'));
                if (modal) modal.hide();
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred.');
            }
        });
    });

    document.getElementById('product_indexing').addEventListener('click', function() {
        new bootstrap.Modal(document.getElementById('indexingModal')).show();
    });
});
</script>
@endsection

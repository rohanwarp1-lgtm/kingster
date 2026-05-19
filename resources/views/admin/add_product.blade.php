<?php $page = 'add_product'; ?>
@extends('layout.mainlayout_admin')

@section('content')

@php
    $isEdit = isset($product) && $product;
    $buttonText = $isEdit ? 'Update Product' : 'Save Product';
    $pageTitle = $isEdit ? 'Edit Product' : 'Add New Product';
    $allVariantsFilled = $isEdit && !empty($product->variant_1_name) && !empty($product->variant_2_name) && !empty($product->variant_3_name);
@endphp


<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">{{ $pageTitle }}</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.warranty.management') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('product.index') }}">Products</a></li>
                        <li class="breadcrumb-item active">{{ $isEdit ? 'Edit' : 'Add' }}</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="{{ route('product.index') }}" class="btn btn-secondary">
                        <i class="fe fe-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ $pageTitle }}</h4>
                </div>
                <div class="card-body">
                    <div class="form" id="productCreationFormWrapper">
                        <form id="productCreationForm" method="POST" action="{{ route('product.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if($isEdit)
                            <input type="hidden" name="id" value="{{ $product->id }}">
                        @endif
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="uc_product_name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="uc_product_name" name="product_name" placeholder="Enter product name" value="{{ old('product_name', $isEdit ? $product->product_name : '') }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="uc_offer_price" class="form-label">Display Offer Price</label>
                                <input type="number" class="form-control" id="uc_offer_price" name="offer_price" placeholder="Enter offer price" value="{{ old('offer_price', $isEdit ? $product->offer_price : '') }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="uc_original_price" class="form-label">Display Original Price</label>
                                <input type="number" class="form-control" id="uc_original_price" name="original_price" placeholder="Enter original price" value="{{ old('original_price', $isEdit ? $product->original_price : '') }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="form-label">Upload default product image (W:620px, H:500px)</label>
                                    <a class="clear-img-btn text-danger clear-btn" data-img="#default_img">Clear</a>
                                </div>
                                <input type="file" class="form-control" id="default_img" name="default_img">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        @if(isset($product) && $product->default_img)
                            <div class="mt-2">
                                <small class="text-muted">Current image:</small>
                                <img src="@prodImage($product->default_img)" alt="Current default image" style="max-width: 100px;">
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="uc_features" class="form-label">Features</label>
                                <textarea class="form-control" id="uc_features" name="features" rows="3" placeholder="Enter features, separated by commas">{{ old('features', $isEdit ? $product->features : '') }}</textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="uc_description" class="form-label">Description</label>
                                <textarea class="form-control" id="uc_description" name="description" rows="4" placeholder="Enter product description (Start each point with '=>')">{{ old('description', $isEdit ? $product->description : '') }}</textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="uc_rating" class="form-label">Rating</label>
                                <input type="number" step="0.1" max="5" min="0" class="form-control" id="uc_rating" name="rating" placeholder="Enter rating" value="{{ old('rating', $isEdit ? $product->rating : '') }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="uc_review_count" class="form-label">Review Count</label>
                                <input type="number" class="form-control" id="uc_review_count" name="review_count" placeholder="Enter Review Count" value="{{ old('review_count', $isEdit ? $product->review_count : '') }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="uc_sold_count" class="form-label">Sold Count</label>
                                <input type="number" class="form-control" id="uc_sold_count" name="sold_count" placeholder="Enter product sold count" value="{{ old('sold_count', $isEdit ? $product->sold_count : '') }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Upload product images</label>
                                <div class="row">
                                    @for ($i = 1; $i <= 6; $i++)
                                        <div class="col-6 col-md-6 mb-2">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <small class="me-2">Image {{ $i }}</small>
                                                <a class="clear-img-btn text-danger clear-btn" data-img="#uc_img_{{ $i }}">Clear</a>
                                            </div>
                                            <input type="file" class="form-control" id="uc_img_{{ $i }}" name="img_{{ $i }}">
                                            @if($isEdit && $product->{'img_' . $i})
                                                <div class="mt-2">
                                                    <small class="text-muted">Current image:</small>
                                                    <img src="@prodImage($product->{'img_' . $i})" alt="Current image {{ $i }}" style="max-width: 80px;">
                                                    <button type="button"
                                                            class="clear-img-btn text-danger clear-btn delete-image-btn bg-white"
                                                            data-product-id="{{ $product->id }}"
                                                            data-image-field="img_{{ $i }}"
                                                            style="margin-left: 10px;">
                                                        Delete
                                                    </button>
                                                </div>
                                            @endif
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="uc_has_variant" name="has_variant" {{ isset($product) && $product->is_variant ? 'checked' : '' }}>
                                    <label class="form-check-label" for="uc_has_variant">Has Variant?</label>
                                </div>
                            </div>
                        </div>
                        <div id="variant-section" style="display:{{ ($isEdit && $product->is_variant) ? 'block' : 'none' }};">
                            <div id="variant-1" style="display:{{ ($isEdit && $product->is_variant) ? 'block' : 'none' }};">
                                <div class="row">
                                    <div class="col-12"><h6>Variant 1</h6></div>
                                    <div class="col-md-3 mb-3">
                                        <input type="text" class="form-control" id="uc_variant_1_name" name="variant_1_name" placeholder="Variant 1 Name" value="{{ old('variant_1_name', $isEdit ? $product->variant_1_name : '') }}">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="number" step="0.01" class="form-control" id="uc_variant_1_price" name="variant_1_price" placeholder="Variant 1 Price" value="{{ old('variant_1_price', $isEdit ? $product->variant_1_price : '') }}">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="number" step="0.01" class="form-control" id="uc_variant_1_oprice" name="variant_1_oprice" placeholder="Variant 1 Original Price" value="{{ old('variant_1_oprice', $isEdit ? $product->variant_1_oprice : '') }}">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="number" step="0.01" class="form-control" id="uc_variant_1_offer" name="variant_1_offer" placeholder="Variant 1 Offer (%)" value="{{ old('variant_1_offer', $isEdit ? $product->variant_1_offer : '') }}">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div id="variant-2" style="display:{{ ($isEdit && $product->is_variant && $product->variant_2_name) ? 'block' : 'none' }};">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between">
                                            <h6>Variant 2</h6>
                                            @if($isEdit && $product->is_variant && $product->variant_2_name)
                                                <button type="button" class="text-danger clear-btn delete-variant-btn clear-btn bg-white" data-variant="2">Delete Variant 2</button>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="text" class="form-control" id="uc_variant_2_name" name="variant_2_name" placeholder="Variant 2 Name" value="{{ old('variant_2_name', $isEdit ? $product->variant_2_name : '') }}">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="number" step="0.01" class="form-control" id="uc_variant_2_price" name="variant_2_price" placeholder="Variant 2 Price" value="{{ old('variant_2_price', $isEdit ? $product->variant_2_price : '') }}">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="number" step="0.01" class="form-control" id="uc_variant_2_oprice" name="variant_2_oprice" placeholder="Variant 2 Original Price" value="{{ old('variant_2_oprice', $isEdit ? $product->variant_2_oprice : '') }}">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="number" step="0.01" class="form-control" id="uc_variant_2_offer" name="variant_2_offer" placeholder="Variant 2 Offer (%)" value="{{ old('variant_2_offer', $isEdit ? $product->variant_2_offer : '') }}">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div id="variant-3" style="display:{{ ($isEdit && $product->is_variant && $product->variant_3_name) ? 'block' : 'none' }};">
                                <div class="row">
                                    <div class="d-flex justify-content-between">
                                        <h6>Variant 3</h6>
                                        @if($isEdit && $product->is_variant && $product->variant_3_name)
                                            <button type="button" class="text-danger clear-btn delete-variant-btn clear-btn bg-white" data-variant="3">Delete Variant 3</button>
                                        @endif
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="text" class="form-control" id="uc_variant_3_name" name="variant_3_name" placeholder="Variant 3 Name" value="{{ old('variant_3_name', $isEdit ? $product->variant_3_name : '') }}">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="number" step="0.01" class="form-control" id="uc_variant_3_price" name="variant_3_price" placeholder="Variant 3 Price" value="{{ old('variant_3_price', $isEdit ? $product->variant_3_price : '') }}">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="number" step="0.01" class="form-control" id="uc_variant_3_oprice" name="variant_3_oprice" placeholder="Variant 3 Original Price" value="{{ old('variant_3_oprice', $isEdit ? $product->variant_3_oprice : '') }}">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="number" step="0.01" class="form-control" id="uc_variant_3_offer" name="variant_3_offer" placeholder="Variant 3 Offer (%)" value="{{ old('variant_3_offer', $isEdit ? $product->variant_3_offer : '') }}">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-12">
                                    <button type="button" class="btn btn-link" id="add-variant-btn" style="display:{{ ($isEdit && $product->is_variant && $product->variant_3_name) ? 'none' : 'block' }};">
                                        Add another variant
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 d-flex gap-2 justify-content-end">
                                <a href="{{ route('product.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn gradientBTN px-4" id="saveProductBtn">
                                    <i class="fe fe-save"></i> {{ $buttonText }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@section('prouctpage-js')
<script>

    window.isEdit = {{ $isEdit ? 'true' : 'false' }};

    document.addEventListener('DOMContentLoaded', function() {
        // AJAX product form submission
        document.getElementById('productCreationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var form = this;
            var formData = new FormData(form);
            var btn = document.getElementById('saveProductBtn');
            btn.disabled = true;
            btn.textContent = 'Saving...';

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
            })
            .then(r => r.json())
            .then(function(res) {
                if (res.status === 1 || res.success) {
                    toastr.success(res.message || 'Product saved!');
                    setTimeout(function() {
                        window.location.href = productIndexUrl;
                    }, 1000);
                } else {
                    toastr.error(res.message || 'Failed to save product');
                    btn.disabled = false;
                    btn.textContent = '{{ $buttonText }}';
                }
            })
            .catch(function() {
                toastr.error('An error occurred. Please try again.');
                btn.disabled = false;
                btn.textContent = '{{ $buttonText }}';
            });
        });

        // Clear image button
        document.querySelectorAll('.clear-btn[data-img]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var target = document.querySelector(this.getAttribute('data-img'));
                if (target) target.value = '';
            });
        });
        document.querySelectorAll('.delete-image-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                if (!confirm('Are you sure you want to delete this image?')) return;

                const productId = this.getAttribute('data-product-id');
                const imageField = this.getAttribute('data-image-field');
                const button = this;

                fetch(deleteProductImageURL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        image_field: imageField
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        button.parentElement.remove();
                    } else {
                        alert('Failed to delete image.');
                    }
                })
                .catch(() => alert('Error deleting image.'));
            });
        });

        document.querySelectorAll('.delete-variant-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var variantNum = this.getAttribute('data-variant');
                var variantSection = document.getElementById('variant-' + variantNum);
                if (variantSection) {
                    variantSection.querySelectorAll('input').forEach(function(input) {
                        input.value = '';
                    });
                    variantSection.style.display = 'none';
                }
                updateAddVariantButton();
            });
        });

        document.getElementById('add-variant-btn').addEventListener('click', function() {
            for (let i = 2; i <= 3; i++) {
                let variantSection = document.getElementById('variant-' + i);
                if (variantSection && variantSection.style.display === 'none') {
                    variantSection.style.display = 'block';
                    break;
                }
            }
            updateAddVariantButton();
        });

        function updateAddVariantButton() {
            let visibleCount = 0;
            for (let i = 1; i <= 3; i++) {
                let variantSection = document.getElementById('variant-' + i);
                if (variantSection && variantSection.style.display !== 'none') {
                    visibleCount++;
                }
            }
            document.getElementById('add-variant-btn').style.display = (visibleCount < 3) ? 'block' : 'none';
        }

        updateAddVariantButton();

        // Show/hide variant section based on checkbox
        document.getElementById('uc_has_variant').addEventListener('change', function() {
            var section = document.getElementById('variant-section');
            var v1 = document.getElementById('variant-1');
            if (this.checked) {
                section.style.display = 'block';
                v1.style.display = 'block';
            } else {
                section.style.display = 'none';
                for (let i = 1; i <= 3; i++) {
                    var vs = document.getElementById('variant-' + i);
                    if (vs) {
                        vs.querySelectorAll('input').forEach(function(inp) { inp.value = ''; });
                        vs.style.display = 'none';
                    }
                }
            }
            updateAddVariantButton();
        });
    });
</script>
@endsection
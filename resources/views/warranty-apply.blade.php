<?php $page = 'warranty-application-form'; ?>
@extends('layout.mainlayout')
@section('content')

@component('components.breadcrumb')
    @slot('title') Warranty Application Form @endslot
    @slot('li_1') / @endslot
    @slot('li_2') Warranty Application Form @endslot
@endcomponent

<style>
    hr { border-color: #203066 !important; }

    .warranty-card {
        border-radius: 1rem;
        width: 72%;
        margin: 0 auto;
    }
    @media (max-width: 768px) {
        .warranty-card { width: 100% !important; }
    }

    /* Declaration box */
    .declaration-box {
        background: #f8f9ff;
        border: 1.5px solid #d0d4f7;
        border-radius: 10px;
        max-height: 220px;
        overflow-y: auto;
        padding: 16px 18px;
        font-size: 13px;
        line-height: 1.7;
        color: #3d4166;
        scrollbar-width: thin;
        scrollbar-color: #667eea #f0f2ff;
    }
    .declaration-box::-webkit-scrollbar { width: 6px; }
    .declaration-box::-webkit-scrollbar-track { background: #f0f2ff; border-radius: 10px; }
    .declaration-box::-webkit-scrollbar-thumb { background: #667eea; border-radius: 10px; }

    /* Checkbox label */
    .declaration-check-label {
        font-size: 13.5px;
        color: #3d4166;
        font-weight: 500;
        cursor: pointer;
        user-select: none;
    }
    .declaration-check-label a {
        color: #667eea;
        text-decoration: underline;
    }

    /* Custom checkbox */
    #termsCheckbox {
        width: 18px;
        height: 18px;
        margin-top: 2px;
        accent-color: #667eea;
        cursor: pointer;
        flex-shrink: 0;
    }

    /* Submit button states */
    #warrantyFormSubmit {
        width: 100%;
        transition: all .2s;
    }
    #warrantyFormSubmit.btn-locked {
        background: #adb5bd !important;
        border-color: #adb5bd !important;
        cursor: not-allowed;
        opacity: 0.8;
    }

    /* Error pulse */
    @keyframes shake {
        0%,100%{ transform:translateX(0); }
        20%{ transform:translateX(-6px); }
        40%{ transform:translateX(6px); }
        60%{ transform:translateX(-4px); }
        80%{ transform:translateX(4px); }
    }
    .shake { animation: shake .4s ease; }
</style>

<div class="page-wrapper">
    <div class="container my-5">
        <div class="warranty-card shadow p-4">
            <h4 class="mb-1 text-center">Warranty Application Form</h4>
            <p class="text-center text-muted mb-4" style="font-size:13px;">
                Fill in the details below to register your product warranty.
            </p>

            <form id="warrantyForm">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Buyer Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="buyerName" placeholder="Enter buyer name">
                        <div class="text-danger error-message" id="error-buyerName"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="mobile" name="mobile"
                            placeholder="Enter 10-digit mobile number"
                            pattern="[0-9]{10}" maxlength="10" minlength="10"
                            oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                        <div class="text-danger error-message" id="error-mobile"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" placeholder="Enter email (optional)">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Purchase Source <span class="text-danger">*</span></label>
                        <select class="form-select" id="purchaseSource">
                            <option selected disabled value="">Select source</option>
                            <option>Online</option>
                            <option>Retail Store</option>
                            <option>Other</option>
                        </select>
                        <div class="text-danger error-message" id="error-purchaseSource"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Purchase Place / Store Name</label>
                    <textarea class="form-control" id="address" rows="2"
                        placeholder="Enter purchase place / store name"></textarea>
                    <select class="form-control d-none" id="addressSelect" name="address">
                        <option value="">Select platform</option>
                        <option value="Amazon">Amazon</option>
                        <option value="Flipkart">Flipkart</option>
                        <option value="Other Platform">Other Platform</option>
                    </select>
                    <div class="text-danger error-message" id="error-address"></div>
                </div>

                @php
                    use App\Models\ProductName;
                    $products = ProductName::where('is_deleted', 0)->get();
                @endphp

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Product Name <span class="text-danger">*</span></label>
                        <select class="form-control" id="productName" name="productName">
                            <option value="">Select product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->name }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <div class="text-danger error-message" id="error-productName"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Product Serial Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="serialNumber"
                            placeholder="Enter product serial number"
                            oninput="this.value=this.value.toUpperCase()">
                        <div class="text-danger error-message" id="error-serialNumber"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Purchase Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="purchaseDate">
                    <div class="text-danger error-message" id="error-purchaseDate"></div>
                </div>

                <hr>

                {{-- ===== WARRANTY DECLARATION ===== --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:14px; color:#203066;">
                        <i class="fas fa-shield-alt me-1" style="color:#667eea;"></i>
                        Warranty Declaration &amp; Acknowledgement
                        <span class="text-danger">*</span>
                    </label>

                    {{-- Scrollable declaration text --}}
                    <div class="declaration-box mb-3" id="declarationBox">
                        @php
                            $declarationText = $generalSettings->warranty_declaration_text ?? '';
                            // Convert newlines to paragraphs
                            $paragraphs = array_filter(array_map('trim', explode("\n\n", $declarationText)));
                        @endphp
                        @foreach($paragraphs as $para)
                            <p style="margin-bottom:10px;">{{ $para }}</p>
                        @endforeach
                    </div>

                    {{-- Checkbox --}}
                    <div class="d-flex align-items-start gap-2" id="terms-check-wrap"
                         style="padding:14px 16px; background:#fff; border:1.5px solid #e0e3f0; border-radius:8px;">
                        <input type="checkbox" id="termsCheckbox" value="1">
                        <label for="termsCheckbox" class="declaration-check-label">
                            I hereby confirm that I have fully read, understood, and voluntarily accept all the
                            warranty terms, conditions, limitations, exclusions, and policies mentioned above.
                            I agree that KINGSTER warranty is limited to manufacturing defects only and
                            I accept full responsibility for data backup and any damages not covered under warranty.
                        </label>
                    </div>
                    <div class="text-danger mt-1" id="error-terms" style="display:none; font-size:13px;">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        You must read and accept the warranty declaration above to submit your application.
                    </div>
                </div>

                <button type="button" id="warrantyFormSubmit"
                    class="btn btn-primary btn-locked mt-2"
                    data-url="{{ route('store.warranty.details', ['type' => 'application']) }}">
                    <i class="fas fa-lock me-2" id="submitIcon"></i>
                    <span id="submitText">Accept Declaration to Submit</span>
                </button>

            </form>
        </div>
    </div>
</div>


{{-- Success Modal --}}
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-header border-0">
                <h5 class="modal-title w-100">Warranty Submitted</h5>
            </div>
            <div class="modal-body p-0">
                <div style="width:60px;height:60px;background:#e8f9f0;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                    <i class="fas fa-check-circle" style="color:#28c76f;font-size:28px;"></i>
                </div>
                <p class="mb-1 text-success fw-bold">Application submitted successfully!</p>
                <p class="text-muted" style="font-size:13px;">Enjoy our 24×7 customer service and peace of mind with your purchase.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Duplicate Serial Modal --}}
<div class="modal fade" id="serialModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-header border-0">
                <h5 class="modal-title w-100">Duplicate Serial Number</h5>
            </div>
            <div class="modal-body p-0">
                <p class="mb-2 text-danger fw-bold" id="duplicate_msg"></p>
                <p>Please check the number and try again with a different serial.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
/* ===== Declaration checkbox logic ===== */
const checkbox   = document.getElementById('termsCheckbox');
const submitBtn  = document.getElementById('warrantyFormSubmit');
const submitIcon = document.getElementById('submitIcon');
const submitText = document.getElementById('submitText');
const errorTerms = document.getElementById('error-terms');
const checkWrap  = document.getElementById('terms-check-wrap');

function syncSubmitBtn() {
    if (checkbox.checked) {
        submitBtn.classList.remove('btn-locked');
        submitBtn.classList.add('btn-success');
        submitBtn.classList.remove('btn-primary');
        submitIcon.className = 'fas fa-paper-plane me-2';
        submitText.textContent = 'Submit Warranty Application';
        errorTerms.style.display = 'none';
        checkWrap.style.borderColor = '#28c76f';
        checkWrap.style.background  = '#f0fdf4';
    } else {
        submitBtn.classList.add('btn-locked');
        submitBtn.classList.remove('btn-success');
        submitBtn.classList.add('btn-primary');
        submitIcon.className = 'fas fa-lock me-2';
        submitText.textContent = 'Accept Declaration to Submit';
        checkWrap.style.borderColor = '#e0e3f0';
        checkWrap.style.background  = '#fff';
    }
}

checkbox.addEventListener('change', syncSubmitBtn);
syncSubmitBtn(); // init state

/* ===== Submit handler ===== */
document.getElementById('warrantyFormSubmit').addEventListener('click', function () {
    // Must accept terms first
    if (!checkbox.checked) {
        errorTerms.style.display = 'block';
        checkWrap.classList.add('shake');
        document.getElementById('declarationBox').scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(() => checkWrap.classList.remove('shake'), 500);
        return;
    }

    // Basic field validation
    let valid = true;
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');

    if (!document.getElementById('buyerName').value.trim()) {
        document.getElementById('error-buyerName').textContent = 'Buyer name is required.';
        valid = false;
    }
    if (!document.getElementById('mobile').value.trim()) {
        document.getElementById('error-mobile').textContent = 'Mobile number is required.';
        valid = false;
    }
    if (!document.getElementById('purchaseSource').value) {
        document.getElementById('error-purchaseSource').textContent = 'Purchase source is required.';
        valid = false;
    }
    if (!document.getElementById('productName').value) {
        document.getElementById('error-productName').textContent = 'Product name is required.';
        valid = false;
    }
    if (!document.getElementById('serialNumber').value.trim()) {
        document.getElementById('error-serialNumber').textContent = 'Serial number is required.';
        valid = false;
    }
    if (!document.getElementById('purchaseDate').value) {
        document.getElementById('error-purchaseDate').textContent = 'Purchase date is required.';
        valid = false;
    }
    if (!valid) return;

    // Determine address
    let purchaseSource = document.getElementById('purchaseSource').value;
    let address = purchaseSource === 'Online'
        ? document.getElementById('addressSelect').value
        : document.getElementById('address').value;

    let submitBtn = this;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';

    $.ajax({
        url: document.getElementById('warrantyFormSubmit').getAttribute('data-url'),
        type: 'POST',
        data: {
            _token:          '{{ csrf_token() }}',
            buyer_name:      document.getElementById('buyerName').value.trim(),
            mobile:          document.getElementById('mobile').value.trim(),
            email:           document.getElementById('email').value.trim(),
            purchase_source: purchaseSource,
            address:         address,
            product_name:    document.getElementById('productName').value,
            serial_number:   document.getElementById('serialNumber').value.trim(),
            purchase_date:   document.getElementById('purchaseDate').value,
            terms_accepted:  checkbox.checked ? '1' : '0',
        },
        success: function (response) {
            if (response.status === 200) {
                $('#warrantyForm')[0].reset();
                checkbox.checked = false;
                syncSubmitBtn();
                $('#successModal').modal('show');
            } else if (response.status === 400) {
                document.getElementById('duplicate_msg').textContent = response.message;
                $('#serialModal').modal('show');
            } else {
                alert(response.message || 'An error occurred. Please try again.');
            }
        },
        error: function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                let errors = xhr.responseJSON.errors;
                if (errors.terms_accepted) {
                    errorTerms.style.display = 'block';
                    checkWrap.classList.add('shake');
                    setTimeout(() => checkWrap.classList.remove('shake'), 500);
                }
            } else {
                alert('Something went wrong. Please try again.');
            }
        },
        complete: function () {
            submitBtn.disabled = false;
            syncSubmitBtn();
        }
    });
});

/* ===== Online source: show platform dropdown ===== */
document.getElementById('purchaseSource').addEventListener('change', function () {
    let isOnline = this.value === 'Online';
    document.getElementById('address').classList.toggle('d-none', isOnline);
    document.getElementById('addressSelect').classList.toggle('d-none', !isOnline);
});
</script>

@endsection

@extends('layout.mainlayout_admin')
@section('content')

<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">General Settings</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.warranty.management') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Settings</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">System Configuration</h4>
                    </div>
                    <div class="card-body">
                        <form id="formGeneralSetting" enctype="multipart/form-data" method="POST" action="{{ route('general.setting.save') }}">
                            @csrf

                            {{-- Branding --}}
                            <div class="mb-4">
                                <h6 class="text-uppercase fw-bold mb-3" style="font-size:11px; letter-spacing:1px; color:var(--primary); border-bottom:2px solid #f0f0f0; padding-bottom:8px;">
                                    Branding &amp; Assets
                                </h6>
                                <div class="row g-3">
                                    @foreach(['brand_logo' => ['Brand Logo', '200x50px'], 'brand_white_logo' => ['White Logo', 'Transparent PNG'], 'brand_fevicon' => ['Favicon', '32x32px']] as $field => [$label, $hint])
                                        <div class="col-md-4">
                                            <label class="form-label">{{ $label }}</label>
                                            @if(isset($generalSettings) && $generalSettings->$field)
                                                <div class="mb-2 p-2 border rounded" style="background:{{ $field === 'brand_white_logo' ? '#1a1f3a' : '#f8f9fa' }}; display:inline-block;">
                                                    <img src="@prodImage($generalSettings->$field)" alt="{{ $label }}" height="40" style="object-fit:contain;">
                                                </div><br>
                                            @endif
                                            <input type="file" accept="image/*" class="form-control" name="{{ $field }}">
                                            <small class="text-muted">{{ $hint }}</small>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Banners --}}
                            <div class="mb-4">
                                <h6 class="text-uppercase fw-bold mb-3" style="font-size:11px; letter-spacing:1px; color:var(--primary); border-bottom:2px solid #f0f0f0; padding-bottom:8px;">
                                    Header Banners
                                </h6>
                                <div class="row g-3">
                                    @for($i = 1; $i <= 4; $i++)
                                        @php $field = 'header_banner_'.$i; @endphp
                                        <div class="col-md-3">
                                            <label class="form-label">Banner {{ $i }}</label>
                                            <div class="mb-2 border rounded overflow-hidden" style="height:80px; background:#f8f9fa; display:flex; align-items:center; justify-content:center;">
                                                @if(isset($generalSettings) && $generalSettings->$field)
                                                    <img src="@prodImage($generalSettings->$field)" style="max-width:100%; max-height:80px; object-fit:contain;" alt="Banner {{ $i }}">
                                                @else
                                                    <span class="text-muted small">No image</span>
                                                @endif
                                            </div>
                                            <input type="file" accept="image/*" class="form-control" name="{{ $field }}">
                                        </div>
                                    @endfor
                                </div>
                            </div>

                            {{-- Contact --}}
                            <div class="mb-4">
                                <h6 class="text-uppercase fw-bold mb-3" style="font-size:11px; letter-spacing:1px; color:var(--primary); border-bottom:2px solid #f0f0f0; padding-bottom:8px;">
                                    Contact &amp; Support
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Support Mobile</label>
                                        <input type="text" class="form-control" name="customer_support_mobile" value="{{ old('customer_support_mobile', $generalSettings->customer_support_mobile ?? '') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Support Email</label>
                                        <input type="email" class="form-control" name="customer_support_email" value="{{ old('customer_support_email', $generalSettings->customer_support_email ?? '') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Office Hours</label>
                                        <input type="text" class="form-control" name="office_time" value="{{ old('office_time', $generalSettings->office_time ?? '') }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Business Stats --}}
                            <div class="mb-4">
                                <h6 class="text-uppercase fw-bold mb-3" style="font-size:11px; letter-spacing:1px; color:var(--primary); border-bottom:2px solid #f0f0f0; padding-bottom:8px;">
                                    Business Stats
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Active Clients</label>
                                        <input type="number" class="form-control" name="active_clients" value="{{ old('active_clients', $generalSettings->active_clients ?? '') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Expert Mechanics</label>
                                        <input type="number" class="form-control" name="expert_mechanics" value="{{ old('expert_mechanics', $generalSettings->expert_mechanics ?? '') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Years Reputation</label>
                                        <input type="number" class="form-control" name="reputation_years" value="{{ old('reputation_years', $generalSettings->reputation_years ?? '') }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Footer --}}
                            <div class="mb-4">
                                <h6 class="text-uppercase fw-bold mb-3" style="font-size:11px; letter-spacing:1px; color:var(--primary); border-bottom:2px solid #f0f0f0; padding-bottom:8px;">
                                    Footer &amp; Social
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">About Us (Line 1)</label>
                                        <textarea class="form-control" name="footer_about_us_1" rows="2">{{ old('footer_about_us_1', $generalSettings->footer_about_us_1 ?? '') }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">About Us (Line 2)</label>
                                        <textarea class="form-control" name="footer_about_us_2" rows="2">{{ old('footer_about_us_2', $generalSettings->footer_about_us_2 ?? '') }}</textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Instagram Link</label>
                                        <input type="text" class="form-control" name="ig_link" value="{{ old('ig_link', $generalSettings->ig_link ?? '') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">WhatsApp Link</label>
                                        <input type="text" class="form-control" name="wp_link" value="{{ old('wp_link', $generalSettings->wp_link ?? '') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Copyright Text</label>
                                        <input type="text" class="form-control" name="footer_copyright" value="{{ old('footer_copyright', $generalSettings->footer_copyright ?? '') }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Reviews --}}
                            <div class="mb-4">
                                <h6 class="text-uppercase fw-bold mb-3" style="font-size:11px; letter-spacing:1px; color:var(--primary); border-bottom:2px solid #f0f0f0; padding-bottom:8px;">
                                    Customer Reviews
                                </h6>
                                @for($i = 1; $i <= 3; $i++)
                                    @php
                                        $prefix = $i == 1 ? 'first' : ($i == 2 ? 'second' : 'third');
                                        $nameField = $prefix . '_reviewer_name';
                                        $msgField  = $prefix . '_reviewer_msg';
                                    @endphp
                                    <div class="row g-3 mb-2">
                                        <div class="col-md-3">
                                            <label class="form-label">Reviewer {{ $i }} Name</label>
                                            <input type="text" class="form-control" name="{{ $nameField }}" value="{{ old($nameField, $generalSettings->$nameField ?? '') }}">
                                        </div>
                                        <div class="col-md-9">
                                            <label class="form-label">Review Message</label>
                                            <input type="text" class="form-control" name="{{ $msgField }}" value="{{ old($msgField, $generalSettings->$msgField ?? '') }}">
                                        </div>
                                    </div>
                                @endfor
                            </div>

                            <div class="pt-2 border-top">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="fe fe-save"></i> Save All Settings
                                </button>
                            </div>

                        </form>
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
    $('#formGeneralSetting').on('submit', function(e) {
        e.preventDefault();
        var $btn = $(this).find('[type=submit]');
        $btn.prop('disabled', true).html('<i class="fe fe-loader"></i> Saving...');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.success) {
                    toastr.success(res.message || 'Settings saved successfully!');
                } else {
                    toastr.error(res.message || 'Failed to save settings');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    var first = Object.values(xhr.responseJSON.errors)[0];
                    toastr.error(first[0]);
                } else {
                    toastr.error('An error occurred. Please try again.');
                }
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fe fe-save"></i> Save All Settings');
            }
        });
    });
});
</script>
@endsection

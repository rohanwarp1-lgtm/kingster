@extends('layout.mainlayout_admin')
@section('title', 'Replacement Policy Editor')
@section('content')

<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Replacement Policy</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.warranty.management') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Replacement Policy Editor</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="{{ route('warranty.replacement.policy') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                        <i class="fe fe-external-link me-1"></i> Preview Page
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Edit Replacement Policy Content</h4>
            </div>
            <div class="card-body">
                <form id="policy-form">
                    @csrf
                    <textarea id="policy-editor" name="replacement_policy_content">{{ $setting->replacement_policy_content }}</textarea>
                    <div class="mt-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fe fe-save me-1"></i> Save Changes
                        </button>
                        <a href="{{ route('warranty.replacement.policy') }}" target="_blank" class="btn btn-outline-info">
                            <i class="fe fe-eye me-1"></i> View Live Page
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('admin_assets/js/ckeditor.js') }}"></script>
<script>
let policyEditor = null;

ClassicEditor
    .create(document.querySelector('#policy-editor'), {
        toolbar: [
            'heading', '|',
            'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
            'outdent', 'indent', '|',
            'insertTable', 'blockQuote', 'undo', 'redo'
        ]
    })
    .then(editor => {
        policyEditor = editor;
    })
    .catch(() => {
        toastr.warning('Rich editor could not load. You can still edit the policy text.');
    });

$('#policy-form').on('submit', function(e) {
    e.preventDefault();

    $.ajax({
        url: '{{ route('admin.replacement.policy.save') }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            replacement_policy_content: policyEditor ? policyEditor.getData() : $('#policy-editor').val(),
        },
        success: function(res) {
            if (res.success) {
                toastr.success(res.message);
            } else {
                toastr.error(res.message);
            }
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Failed to save');
        }
    });
});
</script>
@endpush

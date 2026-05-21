@extends('layout.mainlayout_admin')
@section('title', 'Page Content Editor')
@section('content')

<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Page Content</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.warranty.management') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Legal Page Editor</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="{{ $policyPages[$activePage]['previewUrl'] }}" target="_blank" class="btn btn-outline-secondary btn-sm" id="preview-page-link">
                        <i class="fe fe-external-link me-1"></i> Preview Page
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4 class="card-title mb-0" id="editor-card-title">Edit {{ $policyPages[$activePage]['label'] }}</h4>
                    <div class="btn-group flex-wrap" role="group" aria-label="Legal page selector">
                        @foreach($policyPages as $key => $page)
                            <button type="button"
                                class="btn btn-sm {{ $key === $activePage ? 'btn-primary' : 'btn-outline-primary' }} policy-page-tab"
                                data-page="{{ $key }}">
                                {{ $page['label'] }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form id="policy-form">
                    @csrf
                    <input type="hidden" id="policy-page-key" name="page" value="{{ $activePage }}">
                    <textarea id="policy-editor" name="page_content">{{ $policyPages[$activePage]['content'] }}</textarea>
                    <div class="mt-3 d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-primary">
                            <i class="fe fe-save me-1"></i> Save Changes
                        </button>
                        <a href="{{ $policyPages[$activePage]['previewUrl'] }}" target="_blank" class="btn btn-outline-info" id="view-live-page-link">
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
const policyPages = {{ Illuminate\Support\Js::from($policyPages) }};
let activePolicyPage = @json($activePage);
let policyEditor = null;

function currentEditorContent() {
    return policyEditor ? policyEditor.getData() : $('#policy-editor').val();
}

function syncCurrentPageContent() {
    if (policyPages[activePolicyPage]) {
        policyPages[activePolicyPage].content = currentEditorContent();
    }
}

function setPolicyPage(pageKey) {
    if (!policyPages[pageKey] || pageKey === activePolicyPage) return;

    syncCurrentPageContent();
    activePolicyPage = pageKey;

    $('#policy-page-key').val(pageKey);
    $('#editor-card-title').text('Edit ' + policyPages[pageKey].label);
    $('#preview-page-link, #view-live-page-link').attr('href', policyPages[pageKey].previewUrl);

    $('.policy-page-tab').each(function() {
        const isActive = $(this).data('page') === pageKey;
        $(this).toggleClass('btn-primary', isActive);
        $(this).toggleClass('btn-outline-primary', !isActive);
    });

    if (policyEditor) {
        policyEditor.setData(policyPages[pageKey].content || '');
    } else {
        $('#policy-editor').val(policyPages[pageKey].content || '');
    }
}

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
        toastr.warning('Rich editor could not load. You can still edit the page text.');
    });

$(document).on('click', '.policy-page-tab', function() {
    setPolicyPage($(this).data('page'));
});

$('#policy-form').on('submit', function(e) {
    e.preventDefault();
    syncCurrentPageContent();

    $.ajax({
        url: '{{ route('admin.replacement.policy.save') }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            page: activePolicyPage,
            page_content: policyPages[activePolicyPage].content,
        },
        success: function(res) {
            if (res.success) {
                toastr.success(res.message);
                policyPages[activePolicyPage].isCustom = true;
            } else {
                toastr.error(res.message || 'Failed to save');
            }
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Failed to save');
        }
    });
});
</script>
@endpush

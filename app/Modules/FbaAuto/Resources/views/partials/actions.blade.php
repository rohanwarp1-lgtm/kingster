<div class="btn-group btn-group-sm">
    <button type="button" class="btn btn-primary btn-sm view-btn" data-id="{{ $row->id }}" title="View">
        <i class="bi bi-eye"></i>
    </button>
    <button type="button" class="btn btn-success btn-sm edit-btn" data-id="{{ $row->id }}" title="Edit">
        <i class="bi bi-pencil"></i>
    </button>
    <button type="button" class="btn btn-info btn-sm status-btn" data-id="{{ $row->id }}" data-status="{{ $row->status }}" title="Change Status">
        <i class="bi bi-arrow-repeat"></i>
    </button>
    <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="{{ $row->id }}" title="Delete">
        <i class="bi bi-trash"></i>
    </button>
</div>

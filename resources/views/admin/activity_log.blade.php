<?php $page = 'activity_log'; ?>
@extends('layout.mainlayout_admin')
@section('content')

<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Activity Log</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.warranty.management') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Activity Log</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <form method="GET" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}" style="width:200px;">
                        <select name="type" class="form-select form-select-sm" style="width:140px;">
                            <option value="">All Types</option>
                            <option value="created" {{ request('type') == 'created' ? 'selected' : '' }}>Created</option>
                            <option value="updated" {{ request('type') == 'updated' ? 'selected' : '' }}>Updated</option>
                            <option value="deleted" {{ request('type') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                        </select>
                        <button class="btn btn-primary btn-sm"><i class="fe fe-filter"></i> Filter</button>
                        @if(request()->anyFilled(['search','type']))
                            <a href="{{ route('admin.activity.log') }}" class="btn btn-secondary btn-sm">Clear</a>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Activity ({{ $activities->total() }} records)</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Module</th>
                                        <th>Description</th>
                                        <th>Details</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($activities as $activity)
                                        @php
                                            $event = $activity->event ?? $activity->description;
                                            $badgeClass = match($event) {
                                                'created' => 'badge bg-success',
                                                'updated' => 'badge bg-warning',
                                                'deleted' => 'badge bg-danger',
                                                default   => 'badge bg-secondary'
                                            };
                                            $causer = $activity->causer;
                                            $subject = $activity->subject_type ? class_basename($activity->subject_type) : ($activity->log_name ?? '-');
                                        @endphp
                                        <tr>
                                            <td>{{ $activity->id }}</td>
                                            <td>
                                                @if($causer)
                                                    <span class="fw-600">{{ $causer->username ?? $causer->name ?? '-' }}</span>
                                                    <br><small class="text-muted">{{ $causer->role ?? '' }}</small>
                                                @else
                                                    <span class="text-muted">System</span>
                                                @endif
                                            </td>
                                            <td><span class="{{ $badgeClass }}">{{ ucfirst($event) }}</span></td>
                                            <td>{{ $subject }}</td>
                                            <td>{{ $activity->description }}</td>
                                            <td>
                                                @if($activity->properties && $activity->properties->count())
                                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal{{ $activity->id }}">
                                                        <i class="fe fe-eye"></i>
                                                    </button>
                                                    <div class="modal fade" id="detailModal{{ $activity->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Activity Details</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <pre style="background:#f8f9fa;padding:16px;border-radius:8px;font-size:12px;overflow:auto;">{{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}</pre>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span title="{{ $activity->created_at }}">{{ $activity->created_at->diffForHumans() }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">No activity records found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $activities->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

<?php $page = 'users'; ?>
@extends('layout.mainlayout_admin')
@section('content')

<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">User Management</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.warranty.management') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userCreateModal" onclick="onOpenCreateUserModal()">
                        <i class="fe fe-plus"></i> Create User
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Users List</h4>
                        <select class="form-select form-select-sm" id="user_status_filter" style="width:160px;">
                            <option value="0">Active Users</option>
                            <option value="1">Deleted Users</option>
                        </select>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="datatable_2">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- User Modal --}}
<div class="modal fade" id="userCreateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="userCreationForm" method="POST">
                    @csrf
                    <input type="hidden" id="user_id" name="user_id" value="" autocomplete="off">

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="user_name" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="user_name" name="user_name" placeholder="Enter username" autocomplete="off">
                            <div class="invalid-feedback" id="usernameError"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="user_email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="user_email" name="user_email" placeholder="Enter email" autocomplete="off">
                            <div class="invalid-feedback" id="emailError"></div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="user_role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="user_role" name="user_role">
                                <option value="Super Admin">Super Admin</option>
                                <option value="Sub Admin">Sub Admin</option>
                            </select>
                            <div class="invalid-feedback" id="roleError"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="user_password" class="form-label mb-0">Password <span class="text-danger">*</span></label>
                                <div id="changePasswordWrapper" style="display:none;" class="d-flex align-items-center gap-1">
                                    <input type="checkbox" id="change_password_checkbox" class="form-check-input mt-0">
                                    <label for="change_password_checkbox" class="form-label mb-0 small">Change Password</label>
                                </div>
                            </div>
                            <div class="input-group">
                                <input type="password" class="form-control" id="user_password" name="user_password" placeholder="Enter password" autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1">
                                    <i class="fa fa-eye" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback d-block" id="passwordError"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn gradientBTN" form="userCreationForm">
                    <i class="fe fe-save"></i> Save User
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

// Toastr global options
toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    timeOut: 3000
};

// CSRF on all AJAX
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

// ─── DataTables helper ────────────────────────────────────────────────────────
function makeDataTable(tableId, ajaxUrl, columns, extraData, filterIds) {
    if (!$(tableId).length) return null;

    var dt = $(tableId).DataTable({
        processing   : true,
        serverSide   : true,
        scrollX      : true,
        autoWidth    : false,
        lengthMenu   : [[10, 25, 50, 100], [10, 25, 50, 100]],
        pageLength   : 10,
        columnDefs   : [{ orderable: false, targets: 0 }],
        language     : { processing: '<span class="spinner-border spinner-border-sm me-2"></span>Loading…' },
        ajax: {
            url  : ajaxUrl,
            type : 'POST',
            data : function (d) {
                if (typeof extraData === 'function') extraData(d);
                d._token = $('meta[name="csrf-token"]').attr('content');
            },
            error: function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : 'Failed to load data.';
                toastr.error(msg);
            }
        },
        columns: columns
    });

    // Redraw on filter change
    if (filterIds && filterIds.length) {
        $(filterIds.join(',')).on('change', function () { dt.ajax.reload(null, false); });
    }

    return dt;
}

$(document).ready(function () {

    // ── 1. Warranty DataTable ─────────────────────────────────────────────────
    makeDataTable(
        '#datatable',
        typeof ajaxURL !== 'undefined' ? ajaxURL : '',
        [
            { data: 'action'          },
            { data: 'warranty_status' },
            { data: 'buyer_name'      },
            { data: 'mobile'          },
            { data: 'product_name'    },
            { data: 'serial_number'   },
            { data: 'purchase_date'   },
            { data: 'expiry_date'     }
        ],
        function (d) {
            d.warranty_status = $('#warranty_status').val();
            d.status_filter   = $('#status_filter').val();
        },
        ['#warranty_status', '#status_filter']
    );

    // ── 2. Users DataTable ────────────────────────────────────────────────────
    makeDataTable(
        '#datatable_2',
        typeof userAjaxURL !== 'undefined' ? userAjaxURL : '',
        [
            { data: 'action'   },
            { data: 'username' },
            { data: 'email'    },
            { data: 'role'     }
        ],
        function (d) {
            d.status_filter = $('#user_status_filter').val();
        },
        ['#user_status_filter']
    );

    // ── 3. Products DataTable ─────────────────────────────────────────────────
    makeDataTable(
        '#datatable_1',
        typeof productAjax !== 'undefined' ? productAjax : '',
        [
            { data: 'action'         },
            { data: 'product_name'   },
            { data: 'offer_price'    },
            { data: 'original_price' },
            { data: 'rating'         },
            { data: 'review_count'   },
            { data: 'sold_count'     },
            { data: 'created_by'     },
            { data: 'modified_by'    },
            { data: 'created_at'     },
            { data: 'updated_at'     }
        ],
        function (d) {
            d.status_filter = $('#products_management_status_filter').val();
        },
        ['#products_management_status_filter']
    );

    // ── 4. Product Names DataTable ────────────────────────────────────────────
    makeDataTable(
        '#datatable_3',
        typeof productNameAjaxURL !== 'undefined' ? productNameAjaxURL : '',
        [
            { data: 'action'      },
            { data: 'name'        },
            { data: 'created_by'  },
            { data: 'modified_by' }
        ],
        function (d) {
            d.status_filter = $('#products_name_status_filter').val();
        },
        ['#products_name_status_filter']
    );

    // ── Sidebar Toggle ────────────────────────────────────────────────────────
    $(document).on('click', '#toggle_btn', function () {
        $('body').toggleClass('mini-sidebar');
        localStorage.setItem('sidebar-mini', $('body').hasClass('mini-sidebar'));
        return false;
    });
    $(document).on('mouseenter', '.sidebar', function () {
        if ($('body').hasClass('mini-sidebar')) $('body').addClass('expand-menu');
    });
    $(document).on('mouseleave', '.sidebar', function () {
        if ($('body').hasClass('mini-sidebar')) $('body').removeClass('expand-menu');
    });
    if (localStorage.getItem('sidebar-mini') === 'true') $('body').addClass('mini-sidebar');

    // ── Password visibility toggle ────────────────────────────────────────────
    $(document).on('click', '#togglePassword', function () {
        var pwd  = $('#user_password');
        var icon = $('#togglePasswordIcon');
        if (pwd.attr('type') === 'password') {
            pwd.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            pwd.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    $(document).on('change', '#change_password_checkbox', function () {
        $('#user_password').prop('disabled', !this.checked);
        if (this.checked) $('#user_password').focus();
    });

    // ── User form AJAX ────────────────────────────────────────────────────────
    $(document).on('submit', '#userCreationForm', function (e) {
        e.preventDefault();
        var isEdit         = $('#user_id').val() !== '';
        var changePassword = isEdit && $('#change_password_checkbox').is(':checked');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $.ajax({
            type: 'POST',
            url : userSaveURL,
            data: {
                user_id        : $('#user_id').val(),
                user_name      : $('#user_name').val(),
                user_email     : $('#user_email').val(),
                user_role      : $('#user_role').val(),
                user_password  : $('#user_password').val(),
                change_password: changePassword ? 1 : 0
            },
            success: function (res) {
                if (res.status) {
                    toastr.success(res.message);
                    bootstrap.Modal.getInstance(document.getElementById('userCreateModal')).hide();
                    if ($('#datatable_2').length) $('#datatable_2').DataTable().ajax.reload(null, false);
                } else {
                    toastr.error(res.message || 'Failed to save user');
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    var e = xhr.responseJSON.errors;
                    if (e.user_name)     { $('#user_name').addClass('is-invalid');     $('#usernameError').text(e.user_name[0]); }
                    if (e.user_email)    { $('#user_email').addClass('is-invalid');    $('#emailError').text(e.user_email[0]); }
                    if (e.user_password) { $('#user_password').addClass('is-invalid'); $('#passwordError').text(e.user_password[0]); }
                    if (e.user_role)     { $('#user_role').addClass('is-invalid');     $('#roleError').text(e.user_role[0]); }
                } else {
                    toastr.error('An error occurred. Please try again.');
                }
            }
        });
    });

    // ── Product Name form AJAX ────────────────────────────────────────────────
    $(document).on('submit', '#productNameForm', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url : productNameSaveURL,
            data: {
                product_name_id: $('#product_name_id').val(),
                product_name   : $('#product_name').val()
            },
            success: function (res) {
                if (res.status == 1 || res.success) {
                    toastr.success(res.message || 'Saved!');
                    $('#product_name_id').val('');
                    $('#product_name').val('');
                    $('#productNameFormTitle').text('Add Product Name');
                    $('#productNameSaveBtn').html('<i class="fe fe-plus"></i> Save Name');
                    $('#productNameCancelBtn').hide();
                    if ($('#datatable_3').length) $('#datatable_3').DataTable().ajax.reload(null, false);
                } else {
                    toastr.error(res.message || 'Failed to save');
                }
            },
            error: function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.product_name)
                    ? xhr.responseJSON.errors.product_name[0] : 'An error occurred.';
                toastr.error(msg);
            }
        });
    });
});

// ─── WARRANTY CRUD ────────────────────────────────────────────────────────────

function changeWarrantyStatus(id, currentStatus) {
    Swal.fire({
        title        : 'Change Warranty Status',
        input        : 'select',
        inputOptions : { Pending: 'Pending', Active: 'Active', Expired: 'Expired', Rejected: 'Rejected' },
        inputValue   : currentStatus,
        showCancelButton   : true,
        confirmButtonColor : '#526BDF',
        cancelButtonColor  : '#EC6767',
        confirmButtonText  : 'Update Status',
        cancelButtonText   : 'Cancel'
    }).then(function (result) {
        if (!result.isConfirmed) return;
        $.ajax({
            type: 'POST',
            url : changeWarrantyStatusURL,
            data: { id: id, status: result.value },
            success: function (res) {
                if (res.status === 1) { toastr.success(res.message); $('#datatable').DataTable().ajax.reload(null, false); }
                else toastr.error(res.message);
            }
        });
    });
}

function warrantyDelete(id) {
    Swal.fire({
        title: 'Delete this warranty?', icon: 'warning',
        showCancelButton: true, confirmButtonText: 'Yes, delete it!',
        confirmButtonColor: '#ea5455'
    }).then(function (result) {
        if (!result.isConfirmed) return;
        $.ajax({
            type: 'GET',
            url : deleteWarrantyURL + '?id=' + id,
            success: function (res) {
                if (res.status == 1) { toastr.success(res.message); $('#datatable').DataTable().ajax.reload(null, false); }
                else toastr.error(res.message || 'Failed');
            }
        });
    });
}

function warrantyRestore(id) {
    $.ajax({
        type: 'GET',
        url : restoreWarrantyURL + '?id=' + id,
        success: function (res) {
            if (res.status == 1) { toastr.success(res.message); $('#datatable').DataTable().ajax.reload(null, false); }
            else toastr.error(res.message || 'Failed');
        }
    });
}

// ─── USER CRUD ────────────────────────────────────────────────────────────────

function onOpenCreateUserModal() {
    $('#user_id').val('');
    $('#user_name').val('');
    $('#user_email').val('');
    $('#user_role').val('Super Admin');
    $('#user_password').val('').prop('disabled', false);
    $('#userModalLabel').text('Add New User');
    $('#changePasswordWrapper').hide();
    $('#change_password_checkbox').prop('checked', false);
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');
}

function editUserCredentials(id) {
    $.ajax({
        type: 'GET',
        url : getUserDetails + '?id=' + id,
        success: function (res) {
            if (res.status) {
                $('#user_id').val(res.data.id);
                $('#user_name').val(res.data.name);
                $('#user_email').val(res.data.email);
                $('#user_role').val(res.data.role);
                $('#user_password').val('').prop('disabled', true);
                $('#userModalLabel').text('Edit User');
                $('#changePasswordWrapper').show();
                $('#change_password_checkbox').prop('checked', false);
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                new bootstrap.Modal(document.getElementById('userCreateModal')).show();
            } else {
                toastr.error(res.message || 'User not found');
            }
        }
    });
}

function userDelete(id) {
    Swal.fire({
        title: 'Delete this user?', icon: 'warning',
        showCancelButton: true, confirmButtonText: 'Yes, delete!',
        confirmButtonColor: '#ea5455'
    }).then(function (result) {
        if (!result.isConfirmed) return;
        $.ajax({
            type: 'GET',
            url : userDeleteURL + '?id=' + id,
            success: function (res) {
                if (res.status == 1) { toastr.success(res.message); $('#datatable_2').DataTable().ajax.reload(null, false); }
                else toastr.error(res.message || 'Failed');
            }
        });
    });
}

function userRestore(id) {
    $.ajax({
        type: 'GET',
        url : userRestoreURL + '?id=' + id,
        success: function (res) {
            if (res.status == 1) { toastr.success(res.message); $('#datatable_2').DataTable().ajax.reload(null, false); }
            else toastr.error(res.message || 'Failed');
        }
    });
}

// ─── PRODUCT CRUD ─────────────────────────────────────────────────────────────

function productDelete(id) {
    Swal.fire({
        title: 'Delete this product?', icon: 'warning',
        showCancelButton: true, confirmButtonText: 'Yes, delete!',
        confirmButtonColor: '#ea5455'
    }).then(function (result) {
        if (!result.isConfirmed) return;
        $.ajax({
            type: 'GET',
            url : productDeleteURL + '?id=' + id,
            success: function (res) {
                if (res.status == 1) { toastr.success(res.message); $('#datatable_1').DataTable().ajax.reload(null, false); }
                else toastr.error(res.message || 'Failed');
            }
        });
    });
}

function productRestore(id) {
    $.ajax({
        type: 'GET',
        url : productRestoreURL + '?id=' + id,
        success: function (res) {
            if (res.status == 1) { toastr.success(res.message); $('#datatable_1').DataTable().ajax.reload(null, false); }
            else toastr.error(res.message || 'Failed');
        }
    });
}

// ─── PRODUCT NAME CRUD ────────────────────────────────────────────────────────

function productNameEdit(id) {
    $.ajax({
        type: 'GET',
        url : getProductNameDetailURL + '?id=' + id,
        success: function (res) {
            if (res.status == 1) {
                if (typeof window.productNameEditExtended === 'function') {
                    window.productNameEditExtended(res.data.id, res.data.name);
                } else {
                    $('#product_name_id').val(res.data.id);
                    $('#product_name').val(res.data.name);
                    $('#productNameSaveBtn').html('<i class="fe fe-save"></i> Update Name');
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        }
    });
}

function productNameDelete(id) {
    Swal.fire({
        title: 'Delete this product name?', icon: 'warning',
        showCancelButton: true, confirmButtonText: 'Yes, delete!',
        confirmButtonColor: '#ea5455'
    }).then(function (result) {
        if (!result.isConfirmed) return;
        $.ajax({
            type: 'GET',
            url : deleteProductNameURL + '?id=' + id,
            success: function (res) {
                if (res.status == 1 || res.success) { toastr.success(res.message || 'Deleted!'); $('#datatable_3').DataTable().ajax.reload(null, false); }
                else toastr.error(res.message || 'Failed');
            }
        });
    });
}

function productNameRestore(id) {
    $.ajax({
        type: 'GET',
        url : restoreProductNameURL + '?id=' + id,
        success: function (res) {
            if (res.status == 1 || res.success) { toastr.success(res.message || 'Restored!'); $('#datatable_3').DataTable().ajax.reload(null, false); }
            else toastr.error(res.message || 'Failed');
        }
    });
}

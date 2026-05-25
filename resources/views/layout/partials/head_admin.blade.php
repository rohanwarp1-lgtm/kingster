<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
<meta name="description" content="Kingster Admin Panel - Enterprise Management System">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Kingster Admin')</title>

<link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon.svg') }}">
<link rel="stylesheet" href="{{ asset('admin_assets/plugins/bootstrap/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin_assets/css/animate.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin_assets/plugins/feather/feather.css') }}">
<link rel="stylesheet" href="{{ asset('admin_assets/plugins/fontawesome/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin_assets/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin_assets/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin_assets/plugins/sweetalert2/sweetalert2.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin_assets/css/bootstrap-datetimepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin_assets/plugins/toastr/toastr.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin_assets/css/admin.css') }}">

<style>
    /* ========== KINGSTER ADMIN – DESIGN SYSTEM ========== */
    :root {
        --primary:       #5b5fcf;
        --primary-dark:  #4349b5;
        --primary-light: #7c7fdf;
        --gradient:      linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --gradient-rev:  linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        --success:       #28c76f;
        --warning:       #ff9f43;
        --danger:        #ea5455;
        --info:          #00cfe8;
        --dark:          #2a3042;
        --muted:         #74788d;
        --border:        #e9ecef;
        --bg-body:       #f3f4f8;
        --bg-card:       #ffffff;
        --sidebar-bg:    #0f1535;
        --sidebar-w:     265px;
        --header-h:      70px;
        --datatable-row-h: 52px;
        --datatable-body-h: calc(var(--datatable-row-h) * 10);
        --radius:        14px;
        --radius-sm:     8px;
        --shadow:        0 4px 24px rgba(34,41,47,.08);
        --shadow-md:     0 8px 32px rgba(34,41,47,.14);
    }

    /* ---- Reset / Base ---- */
    html, body { background: var(--bg-body); }
    body { color: var(--dark); font-family: "Inter", sans-serif; font-size: 14px; }
    h1,h2,h3,h4,h5,h6 { color: var(--dark); font-family: "Inter", sans-serif; margin-top: 0; }
    a { text-decoration: none; outline: none; }
    a:hover { text-decoration: none; }

    /* ---- Logo ---- */
    .logo { display: flex; align-items: center; justify-content: center; gap: 10px; padding: 0; }
    .logo-text {
        font-size: 22px; font-weight: 800; letter-spacing: 3px; text-transform: uppercase;
        background: var(--gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .logo-icon {
        width: 36px; height: 36px; background: var(--gradient);
        color: #fff; font-size: 18px; font-weight: 800;
        display: flex; align-items: center; justify-content: center;
        border-radius: 10px; box-shadow: 0 4px 12px rgba(102,126,234,.4);
    }
    .mini-sidebar .logo { display: flex; justify-content: center; }

    /* ---- Sidebar ---- */
    .sidebar {
        background: var(--sidebar-bg) !important;
        width: var(--sidebar-w) !important;
        box-shadow: 4px 0 24px rgba(0,0,0,.15);
    }
    .header-left {
        display: flex; align-items: center; justify-content: flex-start;
        padding: 0 20px; height: var(--header-h);
        border-bottom: 1px solid rgba(255,255,255,.06);
    }
    .header-left .logo { justify-content: flex-start; width: 100%; }

    .sidebar-menu { padding: 8px 12px 40px; }
    .sidebar-menu ul { list-style: none; margin: 0; padding: 0; }

    .menu-title {
        color: rgba(255,255,255,.35); font-size: 10px; font-weight: 700;
        letter-spacing: 1.5px; text-transform: uppercase;
        padding: 20px 14px 6px; display: block; margin: 0;
    }
    .menu-title span { color: rgba(255,255,255,.35); }

    .sidebar-menu li a {
        color: rgba(255,255,255,.7); display: flex; align-items: center;
        font-size: 14px; height: 44px; padding: 0 14px; margin: 2px 0;
        border-radius: var(--radius-sm); transition: all .25s ease;
        font-weight: 500;
    }
    .sidebar-menu li a i { margin-right: 12px; font-size: 16px; width: 18px; text-align: center; }
    .sidebar-menu li a:hover { background: rgba(255,255,255,.08); color: #fff; }
    .sidebar-menu li a:hover i,
    .sidebar-menu li.active > a i { color: #fff !important; }
    .sidebar-menu li.active > a {
        background: var(--gradient) !important; color: #fff !important;
        box-shadow: 0 4px 15px rgba(102,126,234,.35);
    }
    .sidebar-menu li.active > a::after { display: none !important; }

    /* ---- Header ---- */
    .header {
        background: var(--bg-card) !important; height: var(--header-h);
        box-shadow: 0 2px 10px rgba(0,0,0,.06); display: flex;
        align-items: center; justify-content: space-between; gap: 16px;
        padding: 0 24px; position: fixed; top: 0;
        left: var(--sidebar-w); right: 0; z-index: 999;
        transition: left .2s ease;
    }
    .main-wrapper > .header,
    .header.fixed-header { left: var(--sidebar-w); }
    .mini-sidebar .header,
    .mini-sidebar .main-wrapper > .header,
    .mini-sidebar .header.fixed-header { left: 60px; }
    .admin-header-left { display: flex; align-items: center; flex-shrink: 0; }
    .admin-sidebar-toggle,
    #toggle_btn.admin-sidebar-toggle {
        width: 40px; height: 40px; border: 1px solid var(--border);
        border-radius: 10px; background: #fff; color: var(--dark);
        display: inline-flex; align-items: center; justify-content: center;
        cursor: pointer; box-shadow: none; transition: background .2s ease, color .2s ease;
    }
    .admin-sidebar-toggle:hover,
    .admin-sidebar-toggle:focus,
    #toggle_btn.admin-sidebar-toggle:hover,
    #toggle_btn.admin-sidebar-toggle:focus {
        background: #f8f9fa; color: var(--primary); border-color: transparent !important;
        outline: none; box-shadow: none;
    }
    .admin-sidebar-toggle i,
    #toggle_btn.admin-sidebar-toggle i { font-size: 19px; transition: transform .2s ease; }
    .mini-sidebar .admin-sidebar-toggle i,
    .mini-sidebar #toggle_btn.admin-sidebar-toggle i { transform: rotate(180deg); }
    .header-split { display: flex; align-items: center; gap: 20px; }
    .nav-item.dropdown .user-link { display: flex; align-items: center; cursor: pointer; }
    .nav-item.dropdown.has-arrow .nav-link::after { display: none; }
    .user-menu.nav-item { position: relative; }
    .viewsite {
        display: flex; align-items: center; gap: 6px;
        color: var(--muted); font-size: 13px; font-weight: 500;
        padding: 7px 14px; border-radius: var(--radius-sm);
        border: 1px solid var(--border); transition: all .2s ease;
    }
    .viewsite:hover { background: #f8f9fa; color: var(--primary); border-color: transparent !important; box-shadow: none !important; }
    .viewsite i { font-size: 14px; }

    .user-img { display: flex; align-items: center; gap: 10px; }
    .user-avatar {
        width: 38px; height: 38px; background: var(--gradient);
        color: #fff; font-size: 16px; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%; flex-shrink: 0;
    }
    .user-content { display: flex; flex-direction: column; }
    .user-name { font-size: 14px; font-weight: 600; color: var(--dark); line-height: 1.2; }
    .user-details { font-size: 11px; color: var(--muted); }
    .user-menu .dropdown-menu {
        border: none; border-radius: var(--radius-sm);
        box-shadow: var(--shadow-md); padding: 8px; min-width: 180px;
        margin-top: 8px;
    }
    .user-menu .dropdown-item {
        border-radius: 6px; padding: 8px 12px; font-size: 13px;
        font-weight: 500; color: var(--dark); display: flex; align-items: center; gap: 8px;
    }
    .user-menu .dropdown-item:hover { background: #f3f4f8; color: var(--primary); }
    .animate-circle { display: none; }

    /* ---- Page wrapper ---- */
    .page-wrapper {
        margin-left: var(--sidebar-w);
        padding-top: var(--header-h);
        min-height: 100vh;
        background: var(--bg-body);
        transition: margin-left .2s ease;
    }
    .mini-sidebar .page-wrapper { margin-left: 60px; }
    .mini-sidebar.expand-menu .page-wrapper { margin-left: var(--sidebar-w); }
    .mini-sidebar.expand-menu .main-wrapper > .header,
    .mini-sidebar.expand-menu .header.fixed-header { left: var(--sidebar-w); }
    .content { padding: 24px; }

    /* ---- Page header ---- */
    .page-header { margin-bottom: 20px; }
    .page-title { font-size: 22px; font-weight: 700; color: var(--dark); margin: 0; line-height: 1.3; }
    .breadcrumb { background: transparent; padding: 0; margin: 4px 0 0; }
    .breadcrumb-item a { color: var(--primary); font-size: 13px; }
    .breadcrumb-item.active { color: var(--muted); font-size: 13px; }
    .breadcrumb-item + .breadcrumb-item::before { color: var(--muted); }

    /* ---- Cards ---- */
    .card {
        background: var(--bg-card); border: none !important;
        border-radius: var(--radius) !important; box-shadow: var(--shadow);
        transition: box-shadow .2s ease, transform .2s ease;
    }
    .card:hover { box-shadow: var(--shadow-md); }
    .card-header {
        background: transparent !important; border-bottom: 1px solid var(--border) !important;
        border-radius: var(--radius) var(--radius) 0 0 !important;
        padding: 16px 20px !important;
    }
    .card-title { font-size: 15px; font-weight: 600; color: var(--dark); margin: 0; }
    .card-body { padding: 20px !important; }

    /* ---- Stat tiles — hide icon by default (overridden for stat-card below) ---- */
    .db-icon { display: none; }
    .db-widgets { width: 100%; }
    .db-info { display: flex; align-items: baseline; justify-content: space-between; gap: 10px; width: 100%; }
    .db-info h6 { margin: 0; flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .db-info h3 { margin: 0; white-space: nowrap; flex-shrink: 0; }

    /* ---- Stat cards ---- */
    .stat-card {
        border-radius: 8px !important; border: none !important;
        overflow: hidden; transition: opacity .2s ease;
    }
    .stat-card:hover { opacity: .9; }
    .stat-card .card-body { padding: 12px 16px !important; }
    .stat-card .db-widgets { display: flex; align-items: center; justify-content: space-between; gap: 8px; width: 100%; }
    .stat-card .db-info { display: flex; align-items: center; justify-content: space-between; flex: 1; gap: 8px; }
    .stat-card .db-info h6 { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; opacity: .88; margin: 0; flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .stat-card .db-info h3 { font-size: 22px; font-weight: 700; margin: 0; line-height: 1; white-space: nowrap; flex-shrink: 0; }
    .stat-card .db-icon { display: none; }

    .stat-purple { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; }
    .stat-purple .db-info h6, .stat-purple .db-info h3, .stat-purple .db-icon i { color: #fff !important; }

    .stat-green { background: linear-gradient(135deg, #28c76f 0%, #48da89 100%); color: #fff; }
    .stat-green .db-info h6, .stat-green .db-info h3, .stat-green .db-icon i { color: #fff !important; }

    .stat-orange { background: linear-gradient(135deg, #ff9f43 0%, #ffbe76 100%); color: #fff; }
    .stat-orange .db-info h6, .stat-orange .db-info h3, .stat-orange .db-icon i { color: #fff !important; }

    .stat-blue { background: linear-gradient(135deg, #00cfe8 0%, #1eb8d5 100%); color: #fff; }
    .stat-blue .db-info h6, .stat-blue .db-info h3, .stat-blue .db-icon i { color: #fff !important; }

    .stat-red { background: linear-gradient(135deg, #ea5455 0%, #f08080 100%); color: #fff; }
    .stat-red .db-info h6, .stat-red .db-info h3, .stat-red .db-icon i { color: #fff !important; }

    /* ---- Tables ---- */
    .table { border-collapse: separate; border-spacing: 0; }
    .table thead th {
        background: #f8f9fc; color: var(--muted); font-size: 11px;
        font-weight: 700; text-transform: uppercase; letter-spacing: .8px;
        border-bottom: 2px solid var(--border) !important; padding: 12px 16px !important;
        white-space: nowrap;
    }
    .table tbody td {
        padding: 12px 16px !important; vertical-align: middle;
        border-bottom: 1px solid #f0f2f5 !important; font-size: 13.5px;
        color: var(--dark);
    }
    .table tbody tr { transition: background .15s ease; }
    .table tbody tr:hover > td { background: #f8f9ff !important; }
    .table > :not(:first-child) { border-top: 0; }

    /* ---- Badges ---- */
    .badge { border-radius: 6px; padding: 5px 10px; font-weight: 600; font-size: 11px; letter-spacing: .3px; }
    .bg-success { background: var(--success) !important; }
    .bg-warning { background: var(--warning) !important; }
    .bg-danger  { background: var(--danger)  !important; }
    .bg-info    { background: var(--info)    !important; }
    .bg-secondary { background: #6c757d !important; }

    /* ---- Buttons ---- */
    .btn {
        border-radius: var(--radius-sm); font-weight: 600; font-size: 13px;
        padding: 8px 16px; border: none;
        display: inline-flex; align-items: center; gap: 6px;
        transition: background .25s ease, box-shadow .25s ease, border-color .25s ease, color .25s ease, opacity .25s ease;
    }
    .btn:hover,
    .btn:focus,
    .btn:active {
        border-color: transparent !important;
        box-shadow: none !important;
    }
    .btn i,
    .btn [class^="fe-"],
    .btn [class*=" fe-"] {
        color: currentColor !important;
    }
    .btn-primary { background: var(--gradient); color: #fff; border: none; }
    .btn-primary:hover { background: var(--gradient-rev); color: #fff; }
    .btn-success { background: linear-gradient(135deg, #28c76f, #48da89); color: #fff; }
    .btn-success:hover { background: linear-gradient(135deg, #22a45a, #3bc47a); color: #fff; }
    .btn-danger  { background: linear-gradient(135deg, #ea5455, #f08080); color: #fff; }
    .btn-danger:hover  { background: linear-gradient(135deg, #d43f3f, #e06060); color: #fff; }
    .btn-warning { background: linear-gradient(135deg, #ff9f43, #ffbe76); color: #fff; }
    .btn-warning:hover { background: linear-gradient(135deg, #e88a30, #f0a855); color: #fff; }
    .btn-secondary { background: #6c757d; color: #fff; }
    .btn-secondary:hover { background: #5a6268; color: #fff; }
    .btn-outline-primary { border: 1.5px solid var(--primary); color: var(--primary); background: transparent; }
    .btn-outline-primary:hover { background: var(--primary); color: #fff; }
    .btn-outline-secondary { border: 1.5px solid #6c757d; color: #6c757d; background: transparent; }
    .btn-outline-secondary:hover { background: #6c757d; color: #fff; }
    .btn-lg { padding: 12px 24px; font-size: 14px; }
    .btn-sm { padding: 5px 12px; font-size: 12px; }
    .gradientBTN { background: var(--gradient) !important; color: #fff !important; border: none; }
    .gradientBTN:hover { background: var(--gradient-rev) !important; color: #fff !important; }

    /* ---- Form controls ---- */
    .form-control, .form-select {
        border: 1.5px solid #e2e6ea; border-radius: var(--radius-sm);
        padding: 9px 12px; font-size: 13.5px; color: var(--dark);
        transition: border-color .2s ease, box-shadow .2s ease;
        background-color: #fff;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary); box-shadow: 0 0 0 3px rgba(91,95,207,.15); outline: none;
    }
    .form-label { font-weight: 600; color: #444; font-size: 13px; margin-bottom: 6px; }
    .form-select { --bs-form-select-bg-img: unset !important; }

    /* ---- Modals ---- */
    .modal-content { border-radius: var(--radius) !important; border: none !important; box-shadow: var(--shadow-md); }
    .modal-header {
        background: var(--gradient); color: #fff;
        border-radius: var(--radius) var(--radius) 0 0 !important;
        padding: 18px 24px; border-bottom: none;
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px;
    }
    .modal-header .modal-title { font-size: 16px; font-weight: 700; color: #fff; margin: 0; }
    .modal-header .btn-close,
    .modal-header .close,
    .modal-header .close-modal {
        width: 32px; height: 32px; min-width: 32px;
        border: 0 !important; border-radius: 50% !important;
        box-shadow: none !important; outline: 0 !important;
        background-color: rgba(255,255,255,.2) !important;
        color: #fff !important; opacity: 1;
        display: inline-flex; align-items: center; justify-content: center;
        padding: 0 !important; margin: 0 !important;
        line-height: 1;
        transition: background-color .15s ease, opacity .15s ease;
    }
    .modal-header .btn-close { filter: brightness(0) invert(1); }
    .modal-header .btn-close.close-modal { background-image: none !important; filter: none; }
    .modal-header .close { font-size: 24px; font-weight: 400; appearance: none; }
    .modal-header .btn-close:hover,
    .modal-header .btn-close:focus,
    .modal-header .close:hover,
    .modal-header .close:focus,
    .modal-header .close-modal:hover,
    .modal-header .close-modal:focus {
        border: 0 !important; box-shadow: none !important; outline: 0 !important;
        background-color: rgba(255,255,255,.32) !important;
        color: #fff !important; opacity: 1;
    }
    .modal-footer { border-top: 1px solid var(--border); padding: 16px 24px; }
    .modal-body { padding: 24px; }

    /* ---- Pagination ---- */
    .pagination { gap: 0; }
    .page-item .page-link,
    .pagination > li > a,
    .pagination > li > span {
        width: auto; height: auto; min-width: 34px;
        padding: .375rem .75rem;
        margin-left: -1px !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 0 !important;
        background: #fff !important;
        color: #495057;
        font-size: 14px;
        font-weight: 500;
    }
    .page-item:first-child .page-link {
        margin-left: 0 !important;
        border-top-left-radius: .25rem !important;
        border-bottom-left-radius: .25rem !important;
    }
    .page-item:last-child .page-link {
        border-top-right-radius: .25rem !important;
        border-bottom-right-radius: .25rem !important;
    }
    .page-item.active .page-link {
        background: var(--primary) !important;
        border-color: var(--primary) !important;
        color: #fff !important;
    }
    .page-item.disabled .page-link { color: #adb5bd; background: #fff !important; }
    .page-link:hover { background: #e9ecef !important; color: var(--primary); box-shadow: none; }
    .page-link:focus { box-shadow: none; }
    div.dataTables_wrapper div.dataTables_paginate ul.pagination { margin-top: 16px !important; justify-content: flex-end !important; }

    /* ---- DataTables toolbar ---- */
    .dataTables_wrapper { overflow: visible; }
    .card-body > .table-responsive:has(.dataTables_wrapper) { overflow-x: visible; }
    .dataTables_wrapper > .row { margin-left: 0; margin-right: 0; }
    .dataTables_wrapper > .row:first-child,
    .dataTables_wrapper > .row:last-child {
        display: flex; align-items: center; justify-content: space-between;
        gap: 14px 18px; flex-wrap: wrap;
    }
    .dataTables_wrapper > .row:first-child > [class*="col-"],
    .dataTables_wrapper > .row:last-child > [class*="col-"] {
        width: auto; max-width: none; flex: 0 0 auto; padding-left: 0; padding-right: 0;
    }
    .dataTables_wrapper > .row:first-child > [class*="col-"]:last-child,
    .dataTables_wrapper > .row:last-child > [class*="col-"]:last-child { margin-left: auto; }
    .dataTables_wrapper > .row:nth-child(2) {
        margin-left: 0; margin-right: 0; margin-top: 14px;
    }
    .dataTables_wrapper > .row:nth-child(2) > [class*="col-"] {
        min-width: 0; padding-left: 0; padding-right: 0;
    }
    .dataTables_length,
    .dataTables_filter,
    .dataTables_info,
    .dataTables_paginate { margin: 0 !important; overflow: visible; }
    .dataTables_length label,
    .dataTables_filter label {
        color: var(--muted); font-size: 13px; font-weight: 600;
        display: inline-flex; align-items: center; gap: 8px; margin: 0;
    }
    div.dataTables_wrapper div.dataTables_length select {
        width: 76px; min-width: 76px; margin: 0;
    }
    div.dataTables_wrapper div.dataTables_filter { text-align: right; }
    div.dataTables_wrapper div.dataTables_filter input {
        width: 250px; max-width: 100%; margin-left: 8px;
    }
    .dataTables_info {
        padding-top: 0 !important; font-size: 13px; font-weight: 600;
        color: var(--muted); white-space: nowrap;
    }
    div.dataTables_wrapper div.dataTables_paginate ul.pagination {
        margin-top: 0 !important; justify-content: flex-end !important; flex-wrap: nowrap;
    }
    .dataTables_scroll { width: 100%; overflow: visible; }
    .dataTables_scrollHead { overflow: hidden !important; }
    .dataTables_scrollBody {
        height: var(--datatable-body-h) !important;
        min-height: var(--datatable-body-h) !important;
        max-height: var(--datatable-body-h) !important;
        overflow-x: auto !important; overflow-y: auto !important;
        border-bottom: 0 !important; scrollbar-width: thin; padding-bottom: 4px;
        background: #fff;
    }
    .dataTables_scrollBody thead,
    .dataTables_scrollBody thead tr,
    .dataTables_scrollBody thead th {
        height: 0 !important; line-height: 0 !important;
        padding-top: 0 !important; padding-bottom: 0 !important;
        border: 0 !important; visibility: collapse;
    }
    .dataTables_scrollBody thead th::before,
    .dataTables_scrollBody thead th::after { display: none !important; }
    .dataTables_scrollBody table { margin-bottom: 0 !important; }
    .dataTables_scrollBody::-webkit-scrollbar { height: 8px; }
    .dataTables_scrollBody::-webkit-scrollbar-track { background: #f1f3f7; border-radius: 10px; }
    .dataTables_scrollBody::-webkit-scrollbar-thumb { background: #c4c9d4; border-radius: 10px; }
    table.dataTable thead > tr > th.sorting,
    table.dataTable thead > tr > th.sorting_asc,
    table.dataTable thead > tr > th.sorting_desc,
    table.dataTable thead > tr > th.sorting_asc_disabled,
    table.dataTable thead > tr > th.sorting_desc_disabled {
        position: relative; padding-right: 32px !important;
    }
    table.dataTable > thead .sorting:before,
    table.dataTable > thead .sorting_asc:before,
    table.dataTable > thead .sorting_desc:before,
    table.dataTable > thead .sorting_asc_disabled:before,
    table.dataTable > thead .sorting_desc_disabled:before {
        top: 50% !important; right: 12px !important; margin-top: -10px;
        line-height: 1; color: #c1ccdb;
    }
    table.dataTable > thead .sorting:after,
    table.dataTable > thead .sorting_asc:after,
    table.dataTable > thead .sorting_desc:after,
    table.dataTable > thead .sorting_asc_disabled:after,
    table.dataTable > thead .sorting_desc_disabled:after {
        top: 50% !important; right: 12px !important; margin-top: 2px;
        line-height: 1; color: #c1ccdb;
    }

    /* ---- Scrollbar ---- */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #aaa; }

    /* ---- Sidebar mini ---- */
    .mini-sidebar .sidebar { width: 60px !important; }
    .mini-sidebar .header-left { padding: 0 12px; justify-content: center; }
    .mini-sidebar .sidebar .header-left .logo { display: flex; justify-content: center; }
    .mini-sidebar .sidebar .header-left .logo-icon {
        display: flex; width: 36px; min-width: 36px; height: 36px;
    }
    .mini-sidebar .logo-text { display: none; }
    .mini-sidebar .sidebar-menu li a span, .mini-sidebar .menu-title span { display: none; }
    .mini-sidebar .sidebar-menu li a { justify-content: center; padding: 0; }
    .mini-sidebar .sidebar-menu li a i { margin-right: 0; }
    .mini-sidebar.expand-menu .sidebar { width: var(--sidebar-w) !important; }
    .mini-sidebar.expand-menu .header-left { padding: 0 20px; justify-content: flex-start; }
    .mini-sidebar.expand-menu .sidebar .header-left .logo { display: flex; justify-content: flex-start; }
    .mini-sidebar.expand-menu .logo-text { display: inline; }
    .mini-sidebar.expand-menu .sidebar-menu li a span, .mini-sidebar.expand-menu .menu-title span { display: inline; }
    .mini-sidebar.expand-menu .sidebar-menu li a { justify-content: flex-start; padding: 0 14px; }
    .mini-sidebar.expand-menu .sidebar-menu li a i { margin-right: 12px; }

    /* ---- Toastr ---- */
    #toast-container > div { border-radius: 10px; font-size: 13.5px; }

    /* ---- Select2 ---- */
    .select2-container { width: 100% !important; }
    .select2-selection--single { min-height: 38px !important; border: 1.5px solid #e2e6ea !important; border-radius: var(--radius-sm) !important; }
    .select2-selection--single .select2-selection__rendered { line-height: 36px !important; padding-left: 10px !important; }
    .select2-selection--single .select2-selection__arrow { height: 36px !important; }
    .select2-results__option--highlighted { background: var(--primary) !important; }

    /* ---- Tab heading ---- */
    h5.tab-heading {
        font-size: 15px; background: linear-gradient(135deg, #f0f4ff, #f5f0ff);
        padding: 12px 20px; border-radius: var(--radius-sm); margin-bottom: 20px;
        border: 1px solid #dce4ff; color: var(--dark); font-weight: 600;
    }

    /* ---- Mobile header logo (hidden on desktop) ---- */
    .mobile-header-logo { display: none; }

    /* ---- Misc ---- */
    .clear-btn { border: 1px solid var(--danger); border-radius: 4px; padding: 1px 6px; font-size: 11px; cursor: pointer; line-height: 18px; }
    .pos-middle { vertical-align: middle; }
    .list-inline-item a { color: var(--muted); font-size: 16px; transition: color .15s; }
    .list-inline-item a:hover { color: var(--primary); }
    .fe-trash:hover, .fe-trash { color: var(--danger) !important; }
    .fe-rotate-ccw:hover, .fe-rotate-ccw { color: var(--success) !important; }
    .fe-sliders { color: var(--warning) !important; }
    .btn .fe-trash,
    .btn .fe-trash-2,
    .btn .fe-rotate-ccw,
    .btn .fe-sliders,
    .bg-comman i,
    .stat-card i {
        color: #fff !important;
    }

    /* ---- Responsive ---- */
    @media (max-width: 768px) {
        .header,
        .main-wrapper > .header,
        .header.fixed-header { left: 0; }
        .page-wrapper { margin-left: 0; }
        .content { padding: 16px; }
        .page-title { font-size: 18px; }
        .stat-card .db-info h3 { font-size: 24px; }
        .dataTables_wrapper > .row:first-child,
        .dataTables_wrapper > .row:last-child {
            align-items: stretch; flex-direction: column;
        }
        .dataTables_wrapper > .row:first-child > [class*="col-"],
        .dataTables_wrapper > .row:last-child > [class*="col-"] {
            width: 100%; margin-left: 0 !important;
        }
        div.dataTables_wrapper div.dataTables_filter,
        div.dataTables_wrapper div.dataTables_paginate { text-align: left; }
        .dataTables_filter label { width: 100%; }
        div.dataTables_wrapper div.dataTables_filter input {
            width: 100%; min-width: 0; flex: 1 1 auto;
        }
        div.dataTables_wrapper div.dataTables_paginate ul.pagination {
            justify-content: flex-start !important; overflow-x: auto;
        }

        /* Hide desktop-only header elements */
        #toggle_btn { display: none !important; }
        .viewsite { display: none !important; }

        /* Reduce stat tile icon size on mobile */
        .db-icon { width: 38px !important; height: 38px !important; border-radius: 10px !important; }
        .db-icon i { font-size: 18px !important; }

        /* Stat tiles — applied on all module pages */
        .stat-tiles { --bs-gutter-y: 0 !important; }
        .stat-tiles .card { margin-bottom: 15px; }
        .stat-tiles .card-body { padding-top: 8px !important; padding-bottom: 8px !important; }
        #mobile_btn.mobile_btn {
            display: flex; align-items: center; justify-content: center;
            position: static; width: 40px; height: 40px; flex-shrink: 0;
            font-size: 20px; color: var(--dark);
            border: 1px solid var(--border); border-radius: 10px;
            background: #fff; cursor: pointer;
        }

        /* Mobile logo in header */
        .mobile-header-logo {
            display: flex; align-items: center; gap: 8px;
            text-decoration: none; flex: 1; margin-left: 12px;
        }
        .header-split { gap: 12px; margin-right: 5px; }
        .mobile-header-logo .logo-icon {
            width: 32px; height: 32px; min-width: 32px;
            background: var(--gradient); color: #fff;
            font-size: 15px; font-weight: 800;
            display: flex; align-items: center; justify-content: center;
            border-radius: 8px;
        }
        .mobile-header-logo .logo-text {
            font-size: 15px; font-weight: 800; letter-spacing: 2px;
            text-transform: uppercase;
            background: var(--gradient); -webkit-background-clip: text;
            -webkit-text-fill-color: transparent; background-clip: text;
        }

        /* Mobile sidebar — starts below header so header stays always visible */
        .header, .main-wrapper > .header { z-index: 1055 !important; }
        .sidebar {
            margin-left: calc(-1 * var(--sidebar-w)) !important;
            width: var(--sidebar-w) !important;
            position: fixed; top: var(--header-h); height: calc(100vh - var(--header-h));
            z-index: 1050; transition: margin-left .3s ease;
            overflow-x: hidden; overflow-y: auto;
        }
        .sidebar .header-left { display: none; }
        .slide-nav .sidebar { margin-left: 0 !important; }
        #sidebar-overlay { top: var(--header-h) !important; }
        /* Reset mini-sidebar overrides on mobile */
        .mini-sidebar .sidebar,
        .mini-sidebar.expand-menu .sidebar { width: var(--sidebar-w) !important; }
        .mini-sidebar .sidebar-menu li a span,
        .mini-sidebar .menu-title span { display: inline !important; }
        .mini-sidebar .sidebar-menu li a { justify-content: flex-start !important; padding: 0 14px !important; }
        .mini-sidebar .sidebar-menu li a i { margin-right: 12px !important; }
        .sidebar-menu li a span {
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
            flex: 1; min-width: 0;
        }
    }
</style>

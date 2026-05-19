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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.12/sweetalert2.min.css">
<link rel="stylesheet" href="{{ asset('admin_assets/css/bootstrap-datetimepicker.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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
    .logo { display: flex; align-items: center; justify-content: center; padding: 0; }
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
    .mini-sidebar .logo { justify-content: center; }

    /* ---- Sidebar ---- */
    .sidebar {
        background: var(--sidebar-bg) !important;
        width: var(--sidebar-w) !important;
        box-shadow: 4px 0 24px rgba(0,0,0,.15);
    }
    .header-left {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 20px; height: var(--header-h);
        border-bottom: 1px solid rgba(255,255,255,.06);
    }
    .header-left #toggle_btn { color: rgba(255,255,255,.5); font-size: 20px; cursor: pointer; }
    .header-left #toggle_btn:hover { color: #fff; }

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
    .sidebar-menu li.active > a {
        background: var(--gradient) !important; color: #fff !important;
        box-shadow: 0 4px 15px rgba(102,126,234,.35);
    }
    .sidebar-menu li.active > a::after { display: none !important; }

    /* ---- Header ---- */
    .header {
        background: var(--bg-card) !important; height: var(--header-h);
        box-shadow: 0 2px 10px rgba(0,0,0,.06); display: flex;
        align-items: center; justify-content: flex-end;
        padding: 0 24px; position: fixed; top: 0;
        left: var(--sidebar-w); right: 0; z-index: 999;
        transition: left .2s ease;
    }
    .mini-sidebar .header { left: 60px; }
    .header .header-left { display: none !important; }
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
    .viewsite:hover { background: #f8f9fa; color: var(--primary); border-color: var(--primary); }
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

    /* ---- Legacy bg-comman (used by module views) ---- */
    .bg-comman {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        border: none !important; color: #fff;
    }
    .bg-comman .db-info h6 { color: rgba(255,255,255,.85); font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 6px; }
    .bg-comman .db-info h3 { color: #fff; font-size: 28px; font-weight: 700; margin: 0; }
    .bg-comman .db-icon { width: 56px; height: 56px; border-radius: 14px; background: rgba(255,255,255,.2); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .bg-comman .db-icon i { color: #fff !important; font-size: 24px; }

    /* ---- Stat cards ---- */
    .stat-card {
        border-radius: var(--radius) !important; border: none !important;
        overflow: hidden; transition: transform .25s ease, box-shadow .25s ease;
    }
    .stat-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md) !important; }
    .stat-card .card-body { padding: 22px !important; }
    .stat-card .db-info h6 { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .8px; opacity: .85; margin-bottom: 6px; }
    .stat-card .db-info h3 { font-size: 30px; font-weight: 700; margin: 0; line-height: 1; }
    .stat-card .db-icon {
        width: 56px; height: 56px; border-radius: 14px;
        background: rgba(255,255,255,.22); display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .stat-card .db-icon i { font-size: 24px; }

    .stat-purple { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; }
    .stat-purple .db-info h6, .stat-purple .db-info h3, .stat-purple .db-icon i { color: #fff !important; }

    .stat-green { background: linear-gradient(135deg, #28c76f 0%, #48da89 100%); color: #fff; }
    .stat-green .db-info h6, .stat-green .db-info h3, .stat-green .db-icon i { color: #fff !important; }

    .stat-orange { background: linear-gradient(135deg, #ff9f43 0%, #ffbe76 100%); color: #fff; }
    .stat-orange .db-info h6, .stat-orange .db-info h3, .stat-orange .db-icon i { color: #fff !important; }

    .stat-blue { background: linear-gradient(135deg, #00cfe8 0%, #1eb8d5 100%); color: #fff; }
    .stat-blue .db-info h6, .stat-blue .db-info h3, .stat-blue .db-icon i { color: #fff !important; }

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
    .btn:hover { box-shadow: 0 4px 14px rgba(0,0,0,.12); }
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
    }
    .modal-header .modal-title { font-size: 16px; font-weight: 700; color: #fff; }
    .modal-header .btn-close { filter: brightness(0) invert(1); opacity: .8; }
    .modal-footer { border-top: 1px solid var(--border); padding: 16px 24px; }
    .modal-body { padding: 24px; }

    /* ---- Pagination ---- */
    .page-item.active .page-link { background: var(--gradient); border-color: transparent; }
    .page-link { border-radius: 6px !important; margin: 0 2px; color: var(--primary); border-color: var(--border); }
    .page-link:hover { background: #f3f4f8; color: var(--primary); }
    div.dataTables_wrapper div.dataTables_paginate ul.pagination { margin-top: 16px !important; justify-content: center !important; }

    /* ---- DataTables toolbar ---- */
    div#datatable_1_wrapper .row:first-child .col-sm-12,
    div#datatable_2_wrapper .row:first-child .col-sm-12,
    div#datatable_3_wrapper .row:first-child .col-sm-12,
    div#datatable_4_wrapper .row:first-child .col-sm-12,
    div#datatable_wrapper .row:first-child .col-sm-12 { width: 50%; }
    .dataTables_length { margin-top: 0 !important; }
    .dataTables_length label, .dataTables_filter label { color: var(--muted); font-size: 13px; }
    .dataTables_info { font-size: 13px; font-weight: 500; color: var(--muted); }

    /* ---- Scrollbar ---- */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #aaa; }

    /* ---- Sidebar mini ---- */
    .mini-sidebar .sidebar { width: 60px !important; }
    .mini-sidebar .logo-text { display: none; }
    .mini-sidebar .sidebar-menu li a span, .mini-sidebar .menu-title span { display: none; }
    .mini-sidebar .sidebar-menu li a { justify-content: center; padding: 0; }
    .mini-sidebar .sidebar-menu li a i { margin-right: 0; }
    .mini-sidebar.expand-menu .sidebar { width: var(--sidebar-w) !important; }
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

    /* ---- Misc ---- */
    .clear-btn { border: 1px solid var(--danger); border-radius: 4px; padding: 1px 6px; font-size: 11px; cursor: pointer; line-height: 18px; }
    .pos-middle { vertical-align: middle; }
    .list-inline-item a { color: var(--muted); font-size: 16px; transition: color .15s; }
    .list-inline-item a:hover { color: var(--primary); }
    .fe-trash:hover, .fe-trash { color: var(--danger) !important; }
    .fe-rotate-ccw:hover, .fe-rotate-ccw { color: var(--success) !important; }
    .fe-edit:hover, .fe-edit { color: var(--primary) !important; }
    .fe-sliders { color: var(--warning) !important; }

    /* ---- Responsive ---- */
    @media (max-width: 768px) {
        .header { left: 0; }
        .page-wrapper { margin-left: 0; }
        .content { padding: 16px; }
        .page-title { font-size: 18px; }
        .stat-card .db-info h3 { font-size: 24px; }
    }
</style>

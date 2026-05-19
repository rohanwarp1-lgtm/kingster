<div class="sidebar" id="sidebar">
    <div class="header-left">
        <a href="{{ route('admin.warranty.management') }}" class="logo">
            <span class="logo-text">KINGSTER</span>
        </a>
        <a href="javascript:void(0);" id="toggle_btn">
            <i class="fe fe-menu"></i>
        </a>
    </div>
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title"><span>Main</span></li>
                <li class="{{ Route::is('admin.warranty.management') ? 'active' : '' }}">
                    <a href="{{ route('admin.warranty.management') }}">
                        <i class="fe fe-grid"></i> <span>Dashboard</span>
                    </a>
                </li>

                <li class="menu-title"><span>Modules</span></li>
                <li class="{{ Route::is('admin.fba-auto.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.fba-auto.index') }}">
                        <i class="fe fe-truck"></i> <span>FBA Auto</span>
                    </a>
                </li>
                <li class="{{ Route::is('admin.warranty.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.warranty.index') }}">
                        <i class="fe fe-shield"></i> <span>Warranty</span>
                    </a>
                </li>
                <li class="{{ Route::is('admin.rma.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.rma.index') }}">
                        <i class="fe fe-refresh-cw"></i> <span>RMA Management</span>
                    </a>
                </li>
                <li class="{{ Route::is('admin.return-report.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.return-report.index') }}">
                        <i class="fe fe-bar-chart-2"></i> <span>Return Reports</span>
                    </a>
                </li>

                <li class="menu-title"><span>Management</span></li>
                <li class="{{ Route::is('user.index') ? 'active' : '' }}">
                    <a href="{{ route('user.index') }}">
                        <i class="fe fe-users"></i> <span>Users</span>
                    </a>
                </li>
                <li class="{{ Route::is('product.index') || Route::is('create.product.view') || Route::is('edit.product.view') ? 'active' : '' }}">
                    <a href="{{ route('product.index') }}">
                        <i class="fe fe-package"></i> <span>Products</span>
                    </a>
                </li>
                <li class="{{ Route::is('create.product.name.view') ? 'active' : '' }}">
                    <a href="{{ route('create.product.name.view') }}">
                        <i class="fe fe-tag"></i> <span>Product Names</span>
                    </a>
                </li>

                <li class="menu-title"><span>System</span></li>
                <li class="{{ Route::is('general.setting') ? 'active' : '' }}">
                    <a href="{{ route('general.setting') }}">
                        <i class="fe fe-settings"></i> <span>General Settings</span>
                    </a>
                </li>
                <li class="{{ Route::is('admin.activity.log') ? 'active' : '' }}">
                    <a href="{{ route('admin.activity.log') }}">
                        <i class="fe fe-activity"></i> <span>Activity Log</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('index-3') }}" target="_blank">
                        <i class="fe fe-external-link"></i> <span>Visit Site</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('signout') }}">
                        <i class="fe fe-log-out"></i> <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

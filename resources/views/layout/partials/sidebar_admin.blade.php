<div class="sidebar" id="sidebar">
    <div class="header-left">
        <a href="{{ route('admin.warranty.management') }}" class="logo">
            <span class="logo-icon">K</span>
            <span class="logo-text">KINGSTER</span>
        </a>
    </div>
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title"><span>Main</span></li>
                <li class="{{ Route::is('admin.warranty.management') ? 'active' : '' }}">
                    <a href="{{ route('admin.warranty.management') }}">
                        <i class="fe fe-shield"></i> <span>Warranty Dashboard</span>
                    </a>
                </li>
                

                <li class="menu-title"><span>Modules</span></li>
                <li class="{{ Route::is('admin.fba-auto.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.fba-auto.index') }}">
                        <i class="fe fe-truck"></i> <span>FBA Shipment</span>
                    </a>
                </li>
                <li class="{{ Route::is('admin.rma.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.rma.index') }}">
                        <i class="fe fe-refresh-cw"></i> <span>Customer Return</span>
                    </a>
                </li>
                <li class="{{ Route::is('admin.return-report.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.return-report.index') }}">
                        <i class="fe fe-bar-chart-2"></i> <span>FBA ReturnsDashboard</span>
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
            </ul>
        </div>
    </div>
</div>

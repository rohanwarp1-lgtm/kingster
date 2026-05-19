<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">
                    <span>Main</span>
                </li>
                <li>
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fe fe-home"></i> <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="submenu">
                    <a href="#"><i class="fe fe-box"></i> <span>Products</span></a>
                    <ul style="display: none;">
                        <li><a href="{{ route('product.index') }}">Products</a></li>
                        <li><a href="{{ route('create.product.name.view') }}">Product Names</a></li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#"><i class="fe fe-box"></i> <span>Modules</span> <span class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li>
                            <a href="{{ route('admin.fba-auto.index') }}">
                                <i class="fe fe-truck"></i> FBA Shipment
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.warranty.index') }}">
                                <i class="fe fe-shield"></i> Warranty
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.rma.index') }}">
                                <i class="fe fe-refresh-cw"></i> RMA
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.return-report.index') }}">
                                <i class="fe fe-bar-chart"></i> Return Report
                            </a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="{{ route('user.index') }}">
                        <i class="fe fe-user"></i> <span>Users</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('general.setting') }}">
                        <i class="fe fe-settings"></i> <span>Settings</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('signout') }}">
                        <i class="fe fe-logout"></i> <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

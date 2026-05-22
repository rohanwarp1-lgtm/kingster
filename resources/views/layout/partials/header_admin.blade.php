<div class="header">
    {{-- Mobile logo (hidden on desktop, shown on mobile) --}}
    <a href="{{ route('admin.warranty.management') }}" class="mobile-header-logo">
        <span class="logo-icon">K</span>
        <span class="logo-text">KINGSTER</span>
    </a>

    {{-- Desktop sidebar toggle (hidden on mobile via CSS) --}}
    <div class="admin-header-left">
        <button type="button" id="toggle_btn" class="admin-sidebar-toggle" aria-label="Toggle sidebar" aria-expanded="true">
            <i class="fe fe-menu"></i>
        </button>
    </div>

    <div class="header-split">
        <a href="{{ route('index-3') }}" class="viewsite" target="_blank">
            <i class="fe fe-globe"></i>
            <span>Visit Site</span>
        </a>

        <div class="nav-item dropdown has-arrow main-drop">
            <a href="javascript:void(0)" class="dropdown-toggle nav-link user-link p-0" data-bs-toggle="dropdown" style="text-decoration:none;">
                <span class="user-img">
                    {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
                </span>
                <span class="user-content d-none d-md-flex flex-column">
                    <span class="user-name">{{ Auth::user()->username }}</span>
                    <span class="user-details">{{ ucfirst(Auth::user()->role) }}</span>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="{{ route('general.setting') }}">
                    <i class="fe fe-settings"></i> Settings
                </a>
                <div class="dropdown-divider my-1"></div>
                <a class="dropdown-item text-danger" href="{{ route('signout') }}">
                    <i class="fe fe-log-out"></i> Logout
                </a>
            </div>
        </div>

        {{-- Mobile hamburger — inside header-split so it sits next to the avatar --}}
        <a href="javascript:void(0)" id="mobile_btn" class="mobile_btn" aria-label="Toggle mobile menu">
            <i class="fe fe-menu"></i>
        </a>
    </div>
</div>

<header class="nxl-header">
    <div class="header-wrapper">
        <div class="header-left d-flex align-items-center gap-4">
            <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                <div class="hamburger hamburger--arrowturn">
                    <div class="hamburger-box">
                        <div class="hamburger-inner"></div>
                    </div>
                </div>
            </a>
            <div class="nxl-navigation-toggle">
                <a href="javascript:void(0);" id="menu-mini-button">
                    <i class="feather-align-left"></i>
                </a>
                <a href="javascript:void(0);" id="menu-expend-button" style="display: none">
                    <i class="feather-arrow-right"></i>
                </a>
            </div>
            <span class="badge" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;">
                ⛪ Harvesters AI Admin
            </span>
        </div>

        <div class="header-right ms-auto">
            <div class="d-flex align-items-center">

                <!-- Fullscreen -->
                <div class="nxl-h-item d-none d-sm-flex">
                    <div class="full-screen-switcher">
                        <a href="javascript:void(0);" class="nxl-head-link me-0" onclick="$('body').fullScreenHelper('toggle');">
                            <i class="feather-maximize maximize"></i>
                            <i class="feather-minimize minimize"></i>
                        </a>
                    </div>
                </div>

                <!-- Dark/Light Toggle -->
                <div class="nxl-h-item dark-light-theme">
                    <a href="javascript:void(0);" class="nxl-head-link me-0 dark-button">
                        <i class="feather-moon"></i>
                    </a>
                    <a href="javascript:void(0);" class="nxl-head-link me-0 light-button" style="display: none">
                        <i class="feather-sun"></i>
                    </a>
                </div>

                <!-- Admin Profile Dropdown -->
                <div class="dropdown nxl-h-item">
                    <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
                        <div class="avatar-text avatar-md" style="background:#ede9fe;color:#7c3aed;cursor:pointer;">
                            {{ strtoupper(substr(auth('admin')->user()->name, 0, 2)) }}
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                        <div class="dropdown-header">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-md" style="background:#ede9fe;color:#7c3aed;">
                                    {{ strtoupper(substr(auth('admin')->user()->name, 0, 2)) }}
                                </div>
                                <div>
                                    <h6 class="text-dark mb-0">{{ auth('admin')->user()->name }}</h6>
                                    <span class="fs-12 fw-medium text-muted">{{ auth('admin')->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item border-0 w-100 text-start text-danger">
                                <i class="feather-log-out me-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</header>
<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('admin.dashboard') }}" class="b-brand">
                <img src="{{ asset('https://harvestersng.org/wp-content/uploads/2022/04/cropped-Harvesters-Logo.jpg') }}"
                     alt="Harvesters AI"
                     class="logo logo-lg"
                     style="width: 140px; height: auto; display: block; margin: 0 auto;" />
                <img src="{{ asset('https://harvestersng.org/wp-content/uploads/2022/04/cropped-Harvesters-Logo.jpg') }}" alt="" class="logo logo-sm" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="nxl-navbar">
                <li class="nxl-item nxl-caption">
                    <label>Harvesters AI</label>
                </li>

                <!-- Dashboard -->
                <li class="nxl-item">
                    <a href="{{ route('admin.dashboard') }}" class="nxl-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="nxl-micon"><i class="feather-home"></i></span>
                        <span class="nxl-mtext">Dashboard</span>
                    </a>
                </li>

                <!-- Members -->
                <li class="nxl-item">
                    <a href="{{ route('admin.members.index') }}" class="nxl-link {{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
                        <span class="nxl-micon"><i class="feather-users"></i></span>
                        <span class="nxl-mtext">Members</span>
                    </a>
                </li>

                <!-- Programs & Events -->
                <li class="nxl-item">
                    <a href="{{ route('admin.programs.index') }}" class="nxl-link {{ request()->routeIs('admin.programs.*') ? 'active' : '' }}">
                        <span class="nxl-micon"><i class="feather-calendar"></i></span>
                        <span class="nxl-mtext">Programs & Events</span>
                    </a>
                </li>

                <!-- Newsletters -->
                <li class="nxl-item">
                    <a href="{{ route('admin.newsletters.index') }}" class="nxl-link {{ request()->routeIs('admin.newsletters.*') ? 'active' : '' }}">
                        <span class="nxl-micon"><i class="feather-send"></i></span>
                        <span class="nxl-mtext">Newsletters</span>
                    </a>
                </li>

                <li class="nxl-item nxl-caption">
                    <label>Church Data</label>
                </li>

                <!-- Campuses -->
                <li class="nxl-item">
                    <a href="{{ route('admin.campuses.index') }}" class="nxl-link {{ request()->routeIs('admin.campuses.*') ? 'active' : '' }}">
                        <span class="nxl-micon"><i class="feather-map-pin"></i></span>
                        <span class="nxl-mtext">Campuses</span>
                    </a>
                </li>

                <!-- Leaders -->
                <li class="nxl-item">
                    <a href="{{ route('admin.leaders.index') }}" class="nxl-link {{ request()->routeIs('admin.leaders.*') ? 'active' : '' }}">
                        <span class="nxl-micon"><i class="feather-star"></i></span>
                        <span class="nxl-mtext">Leaders</span>
                    </a>
                </li>

                <!-- AI Knowledge Base -->
                <li class="nxl-item">
                    <a href="{{ route('admin.church-info.index') }}" class="nxl-link {{ request()->routeIs('admin.church-info.*') ? 'active' : '' }}">
                        <span class="nxl-micon"><i class="feather-book-open"></i></span>
                        <span class="nxl-mtext">AI Knowledge Base</span>
                    </a>
                </li>

                <li class="nxl-item nxl-caption">
                    <label>Account</label>
                </li>

                <!-- Settings -->
                <li class="nxl-item nxl-hasmenu {{ request()->routeIs('admin.profile.*') ? 'nxl-trigger' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-settings"></i></span>
                        <span class="nxl-mtext">Settings</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link" href="#">My Profile</a>
                        </li>
                    </ul>
                </li>

                <!-- Logout -->
                <li class="nxl-item">
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="nxl-link border-0 bg-transparent w-100 text-start">
                            <span class="nxl-micon"><i class="feather-power text-danger"></i></span>
                            <span class="nxl-mtext text-danger">Logout</span>
                        </button>
                    </form>
                </li>

            </ul>
        </div>
    </div>
</nav>
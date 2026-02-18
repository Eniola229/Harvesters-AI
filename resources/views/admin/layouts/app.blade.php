<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Harvesters AI</title>

    <!-- Using the same NXL template assets as the existing dashboard -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}">
    <style>
        .harvesters-badge {
            background: linear-gradient(135deg, #7c3aed, #4f46e5);
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        .stat-card { border-left: 4px solid #7c3aed; }
        .sidebar-brand-text { color: #7c3aed; font-weight: 700; }
    </style>
    @stack('styles')
</head>
<body class="nxl-body">

<div id="pcoded" class="nxl-container">
    <!-- Sidebar -->
    <nav class="nxl-navigation">
        <div class="navbar-wrapper">
            <div class="m-header">
                <a href="{{ route('admin.dashboard') }}" class="b-brand">
                    <span class="sidebar-brand-text fs-5">⛪ Harvesters AI</span>
                </a>
            </div>
            <div class="navbar-content">
                <ul class="nxl-navbar">
                    <li class="nxl-item nxl-caption"><label>Main Menu</label></li>

                    <li class="nxl-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-airplay"></i></span>
                            <span class="nxl-mtext">Dashboard</span>
                        </a>
                    </li>

                    <li class="nxl-item {{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.members.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">Members</span>
                        </a>
                    </li>

                    <li class="nxl-item {{ request()->routeIs('admin.programs.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.programs.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-calendar"></i></span>
                            <span class="nxl-mtext">Programs & Events</span>
                        </a>
                    </li>

                    <li class="nxl-item {{ request()->routeIs('admin.newsletters.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.newsletters.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-send"></i></span>
                            <span class="nxl-mtext">Newsletters</span>
                        </a>
                    </li>

                    <li class="nxl-item nxl-caption"><label>Church Data</label></li>

                    <li class="nxl-item {{ request()->routeIs('admin.campuses.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.campuses.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-map-pin"></i></span>
                            <span class="nxl-mtext">Campuses</span>
                        </a>
                    </li>

                    <li class="nxl-item {{ request()->routeIs('admin.leaders.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.leaders.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-star"></i></span>
                            <span class="nxl-mtext">Leaders</span>
                        </a>
                    </li>

                    <li class="nxl-item {{ request()->routeIs('admin.church-info.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.church-info.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-book-open"></i></span>
                            <span class="nxl-mtext">AI Knowledge Base</span>
                        </a>
                    </li>

                    <li class="nxl-item nxl-caption"><label>Account</label></li>

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

    <!-- Header -->
    <header class="nxl-header">
        <div class="header-wrapper">
            <div class="header-left d-flex align-items-center gap-4">
                <div class="nxl-navigation-toggle">
                    <a href="javascript:void(0);" id="menu-mini-button">
                        <i class="feather-align-left"></i>
                    </a>
                </div>
                <div>
                    <span class="harvesters-badge">Admin Panel</span>
                </div>
            </div>
            <div class="header-right ms-auto">
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted fs-13">Welcome, {{ auth('admin')->user()->name }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="feather-log-out me-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="nxl-container">
        <div class="nxl-content">
            <!-- Page Header -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">@yield('page-title', 'Dashboard')</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        @yield('breadcrumb')
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    @yield('page-actions')
                </div>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mx-4" role="alert">
                    <i class="feather-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mx-4" role="alert">
                    <i class="feather-alert-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Main Content -->
            <div class="main-content">
                @yield('content')
            </div>
        </div>
    </main>
</div>

<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/app.min.js') }}"></script>
@stack('scripts')
</body>
</html>
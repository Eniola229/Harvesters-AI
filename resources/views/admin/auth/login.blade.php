<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Harvesters AI || Admin Login</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('https://harvestersng.org/wp-content/uploads/2022/04/cropped-Harvesters-Logo.jpg') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/theme.min.css') }}" />
</head>
<body class="nxl-body auth-body">

<main class="auth-cover-wrapper">
    <div class="auth-cover-content-inner">
        <div class="auth-cover-content-wrapper">
            <div class="auth-img-content">
                <img src="{{ asset('https://harvestersng.org/wp-content/uploads/2022/08/appz2.png') }}" alt="" class="img-fluid" />
            </div>
        </div>
    </div>
    <div class="auth-cover-sidebar-inner">
        <div class="auth-cover-card-wrapper">
            <div class="auth-cover-card p-sm-5">
                <div class="mb-5 text-center">
                    <a href="#" class="b-brand">
                        <img src="{{ asset('https://harvestersng.org/wp-content/uploads/2022/04/cropped-Harvesters-Logo.jpg') }}" alt="Harvesters AI" style="height: 50px;" />
                    </a>
                    <h2 class="mt-4 fw-bold">Harvesters AI</h2>
                    <p class="text-muted">Sign in to the Admin Dashboard</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-4">
                        <i class="feather-alert-circle me-2"></i>
                        {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('status'))
                    <div class="alert alert-success mb-4">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('admin.login') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="admin@harvestersai.com"
                               value="{{ old('email') }}" required autofocus />
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Enter your password" required />
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="feather-log-in me-2"></i> Sign In
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/vendors/js/vendors.min.js') }}"></script>
</body>
</html>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ExpenseTracker')</title>
    
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  </head>
   <!-- ADDED THIS: Bootstrap Icons CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    
    <!-- LINK YOUR PUBLIC STYLESHEET HERE -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/json2/20160511/json2.min.js" integrity="sha512-uWk2ZXl3GVrq6DZsrL5dSg1S/F3MNQ9VaDFigqXOoKUnWG58UxOuJGfTrzh5KjpoBvPiFniL2PahU2HUTFMJXw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @stack('styles')
</head>
<body class="bg-light">

    @if(Request::is('login') || Request::is('register'))
        <div class="container">
            <div class="row min-vh-100 align-items-center justify-content-center px-3">
                <div class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4">
                    @yield('auth-content')
                </div>
            </div>
        </div>
    @else
        <div class="container-fluid">
            <div class="row">
                @include('components.sidebar')

                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pb-5">
                    <div class="py-3 mb-4 border-bottom">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ asset('icons/logo.png') }}" width="40" alt="ExpenseTracker Logo">
                                <span class="fw-bold fs-5 mb-0">ExpenseTracker</span>
                            </div>
                            <div class="d-flex align-items-center gap-2 mobile-nav-actions">
                                <a href="/scan" class="btn btn-dark btn-sm d-md-none">
                                    <i class="bi bi-qr-code-scan me-1"></i> Scan
                                </a>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-semibold d-none d-sm-inline">{{Auth::user()->name}}</span>
                                    <a href="/profile">
                                        <div class="bg-secondary rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                            <i class="bi bi-person"></i>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <h5 class="m-0 fw-bold">@yield('page-title', 'Welcome back!')</h5>
                            <small class="text-muted">@yield('page-subtitle', 'Manage your financials easily')</small>
                        </div>
                    </div>

                    @yield('content')
                </main>

                @include('components.bottom-nav')
            </div>
        </div>
    @endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    
    <!-- LINK YOUR PUBLIC JAVASCRIPT HERE -->
    <script src="{{ asset('js/custom.js') }}"></script>
    
    @stack('scripts')
</body>
</html>

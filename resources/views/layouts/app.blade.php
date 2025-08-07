<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AbsensiPro - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <style>
        .navbar {
            padding: 1rem;
        }

        .nav-link:hover {
            color: #3b82f6 !important;
        }

        .nav-link.active {
            font-weight: bold;
        }

    </style>
    @stack('styles')
</head>
<body class="bg-gray-50">
    <nav class="navbar navbar-expand-lg bg-light shadow-sm border-bottom border-gray-100">
        <div class="container">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 32px; height: 32px; background-color: #0d6efd;">
                    <i class="ri-calendar-check-line text-white fs-5"></i>
                </div>
                <span class="fs-5 fw-bold text-dark">AbsensiPro</span>
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse pt-3 pt-md-0" id="navbarNav">
                <ul class="navbar-nav mx-auto gap-3">
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('workers.index') ? 'active' : '' }}" href="{{ route('workers.index') }}">Pekerja</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('projects.index') ? 'active' : '' }}" href="{{ route('projects.index') }}">Proyek</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('attendances.create') ? 'active' : '' }}" href="{{ route('attendances.create') }}">Input Absensi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('attendances.index') ? 'active' : '' }}" href="{{ route('attendances.index') }}">Lihat Absensi</a>
                    </li>   
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('attendances.report') ? 'active' : '' }}" href="{{ route('attendances.report') }}">Laporan</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="ri-user-line me-1"></i> Admin
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">Profil</a></li>
                            <li><a class="dropdown-item" href="#">Pengaturan</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                    Keluar
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="content-wrapper">
        <div class="container mt-4">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

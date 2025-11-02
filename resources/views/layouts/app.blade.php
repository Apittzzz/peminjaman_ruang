<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Peminjaman Ruang')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-building"></i> Sistem Peminjaman Ruang
            </a>
            
            @auth
            <div class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> {{ Auth::user()->nama }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><span class="dropdown-item-text">
                            <small>Role: {{ ucfirst(Auth::user()->role) }}</small>
                        </span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </div>
            @else
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('login') }}">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
                <a class="nav-link" href="{{ route('register') }}">
                    <i class="fas fa-user-plus"></i> Register
                </a>
            </div>
            @endauth
        </div>
    </nav>

    <div class="container mt-3">
        <!-- Tampilkan status messages -->
        @if(session('status'))
            <div class="alert alert-info alert-dismissible fade show">
                <i class="fas fa-info-circle"></i> {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    @auth
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-home"></i> Dashboard Admin
                            </a>
                        @elseif(Auth::user()->role === 'petugas')
                            <a href="{{ route('petugas.dashboard') }}">
                                <i class="fas fa-home"></i> Dashboard Petugas
                            </a>
                        @else
                            <a href="{{ route('peminjam.dashboard') }}">
                                <i class="fas fa-home"></i> Dashboard Peminjam
                            </a>
                        @endif
                    @else
                        <!--<a href="{{ url('/') }}"><i class="fas fa-home"></i> Home</a> -->
                    @endauth
                </li>
                @yield('breadcrumb')
            </ol>
        </nav>

        <!-- Content -->
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle logout form submission
        document.addEventListener('DOMContentLoaded', function() {
            const logoutForm = document.getElementById('logoutForm');
            if (logoutForm) {
                logoutForm.addEventListener('submit', function(e) {
                    if (!confirm('Apakah Anda yakin ingin logout?')) {
                        e.preventDefault();
                    }
                });
            }

            // Auto-hide alerts after 5 seconds
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
</body>
</html>
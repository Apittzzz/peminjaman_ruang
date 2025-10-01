<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Peminjaman Ruang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .dashboard-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
        }
        .quick-actions {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-building"></i> Sistem Peminjaman Ruang
            </a>
            <div class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> {{ Auth::user()->nama }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <!-- Quick Actions -->
        <div class="quick-actions">
            <div class="row">
                <div class="col-md-8">
                    <h4><i class="fas fa-tachometer-alt"></i> Dashboard Admin</h4>
                    <p class="mb-0">Selamat datang, {{ Auth::user()->nama }}! Apa yang ingin Anda lakukan hari ini?</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="btn-group">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light">
                            <i class="fas fa-users"></i> Kelola User
                        </a>
                        <a href="{{ route('admin.ruang.index') }}" class="btn btn-light">
                            <i class="fas fa-door-open"></i> Kelola Ruang
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card dashboard-card text-white bg-primary">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <h5>Total Users</h5>
                        <div class="stat-number">{{ \App\Models\User::count() }}</div>
                        <small>Pengguna Terdaftar</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card text-white bg-success">
                    <div class="card-body text-center">
                        <i class="fas fa-door-open fa-3x mb-3"></i>
                        <h5>Total Ruang</h5>
                        <div class="stat-number">{{ \App\Models\Ruang::count() }}</div>
                        <small>Ruang Tersedia</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card text-white bg-warning">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-check fa-3x mb-3"></i>
                        <h5>Peminjaman</h5>
                        <div class="stat-number">0</div>
                        <small>Peminjaman Aktif</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card text-white bg-info">
                    <div class="card-body text-center">
                        <i class="fas fa-file-alt fa-3x mb-3"></i>
                        <h5>Laporan</h5>
                        <div class="stat-number">0</div>
                        <small>Laporan Generated</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Features Grid -->
        <div class="row">
            <!-- Manajemen User -->
            <div class="col-md-6 mb-4">
                <div class="card dashboard-card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-users"></i> Manajemen User</h5>
                    </div>
                    <div class="card-body">
                        <p>Kelola data pengguna sistem (admin, petugas, peminjam)</p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-list"></i> Lihat Daftar User
                            </a>
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah User Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Manajemen Ruang -->
            <div class="col-md-6 mb-4">
                <div class="card dashboard-card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-door-open"></i> Manajemen Ruang</h5>
                    </div>
                    <div class="card-body">
                        <p>Kelola data ruangan yang dapat dipinjam</p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.ruang.index') }}" class="btn btn-outline-success">
                                <i class="fas fa-list"></i> Lihat Daftar Ruang
                            </a>
                            <a href="{{ route('admin.ruang.create') }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> Tambah Ruang Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Generate Laporan -->
            <div class="col-md-6 mb-4">
                <div class="card dashboard-card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-file-alt"></i> Generate Laporan</h5>
                    </div>
                    <div class="card-body">
                        <p>Generate laporan peminjaman ruang dalam berbagai format</p>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-info">
                                <i class="fas fa-file-pdf"></i> Laporan PDF
                            </button>
                            <button class="btn btn-info">
                                <i class="fas fa-file-excel"></i> Laporan Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="col-md-6 mb-4">
                <div class="card dashboard-card">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Statistik Cepat</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Admin Terdaftar
                                <span class="badge bg-primary rounded-pill">
                                    {{ \App\Models\User::where('role', 'admin')->count() }}
                                </span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Petugas Terdaftar
                                <span class="badge bg-info rounded-pill">
                                    {{ \App\Models\User::where('role', 'petugas')->count() }}
                                </span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Peminjam Terdaftar
                                <span class="badge bg-success rounded-pill">
                                    {{ \App\Models\User::where('role', 'peminjam')->count() }}
                                </span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Ruang Tersedia
                                <span class="badge bg-success rounded-pill">
                                    {{ \App\Models\Ruang::where('status', 'kosong')->count() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
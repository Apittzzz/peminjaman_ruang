<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Peminjaman Ruang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .action-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            background: white;
            padding: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }

        .action-card .fas {
            color: #2c3e50;
            margin-bottom: 1.5rem;
        }

        .action-card h4 {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .action-card p {
            color: #6c757d;
        }

        .btn-primary {
            background: #2c3e50;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #1a252f;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .navbar {
            background: #2c3e50 !important; /* Update navbar color to match theme */
        }
    </style>
</head>
<body class="bg-light">
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

    <div class="container py-4">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="action-card text-center">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <h4>Manajemen User</h4>
                    <p class="text-muted">Kelola data pengguna sistem</p>
                    <div class="small text-muted mb-3">
                        <ul class="text-start">
                            <li>Tambah user baru (admin/petugas/peminjam)</li>
                            <li>Edit informasi user yang ada</li>
                            <li>Nonaktifkan akun jika diperlukan</li>
                            <li>Atur hak akses pengguna</li>
                        </ul>
                    </div>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-primary">Kelola User</a>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-outline-secondary">Tambah User</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="action-card text-center">
                    <i class="fas fa-door-open fa-3x mb-3"></i>
                    <h4>Manajemen Ruang</h4>
                    <p class="text-muted">Kelola data ruangan</p>
                    <div class="small text-muted mb-3">
                        <ul class="text-start">
                            <li>Tambah ruangan baru ke sistem</li>
                            <li>Update informasi ruangan</li>
                            <li>Atur kapasitas dan fasilitas</li>
                            <li>Kelola status ketersediaan</li>
                        </ul>
                    </div>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('admin.ruang.index') }}" class="btn btn-primary">Kelola Ruang</a>
                        <a href="{{ route('admin.ruang.create') }}" class="btn btn-outline-secondary">Tambah Ruang</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
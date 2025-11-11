@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">
    <i class="fas fa-tachometer-alt"></i> Dashboard Admin
</li>
@endsection

@section('content')
<div class="container py-4">
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
    </style>

    <div class="row g-4 justify-content-center">
        <div class="col-md-4">
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

        <div class="col-md-4">
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
                    <a href="{{ route('admin.ruang.index') }}" class="btn btn-primary">Kelola</a>
                    <a href="{{ route('admin.ruang.create') }}" class="btn btn-outline-secondary">Tambah Ruang</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="action-card text-center">
                <i class="fas fa-tasks fa-3x mb-3"></i>
                <h4>Manajemen Peminjaman</h4>
                <p class="text-muted">Kelola permohonan peminjaman</p>
                <div class="small text-muted mb-3">
                    <ul class="text-start">
                        <li>Setujui atau tolak peminjaman</li>
                        <li>Lihat detail permohonan</li>
                        <li>Kelola jadwal penggunaan ruang</li>
                        <li>Generate laporan peminjaman</li>
                    </ul>
                </div>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{ route('admin.peminjaman.index') }}" class="btn btn-primary">Kelola</a>
                    <a href="{{ route('admin.laporan.index') }}" class="btn btn-outline-secondary">Laporan</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
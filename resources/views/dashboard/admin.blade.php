@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">
    <i class="fas fa-tachometer-alt"></i> Dashboard Admin
</li>
@endsection

@section('content')
<div class="container py-4">
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
                        <li>Monitor status peminjaman</li>
                    </ul>
                </div>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{ route('admin.peminjaman.index') }}" class="btn btn-primary">Kelola</a>
                    <a href="{{ route('jadwal.index') }}" class="btn btn-outline-secondary">Jadwal</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="action-card text-center">
                <i class="fas fa-file-alt fa-3x mb-3"></i>
                <h4>Laporan</h4>
                <p class="text-muted">Generate dan lihat laporan</p>
                <div class="small text-muted mb-3">
                    <ul class="text-start">
                        <li>Laporan peminjaman per periode</li>
                        <li>Statistik penggunaan ruangan</li>
                        <li>Top peminjam & ruangan populer</li>
                        <li>Export data ke CSV/Excel</li>
                    </ul>
                </div>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{ route('admin.laporan.index') }}" class="btn btn-primary">Lihat Laporan</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
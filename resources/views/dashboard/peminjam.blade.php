@extends('layouts.app')

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
                <i class="fas fa-calendar-plus fa-3x mb-3 "></i>
                <h4>Ajukan Peminjaman</h4>
                <p class="text-muted">Ajukan permohonan peminjaman ruang baru</p>
                <div class="small text-muted mb-3">
                    <ul class="text-start">
                        <li>Pilih ruangan yang tersedia</li>
                        <li>Tentukan tanggal dan waktu peminjaman</li>
                        <li>Jelaskan keperluan penggunaan ruang</li>
                        <li>Tunggu persetujuan dari admin/petugas</li>
                    </ul>
                </div>
                <a href="{{ route('peminjam.peminjaman.create') }}" class="btn btn-primary">Ajukan Sekarang</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="action-card text-center">
                <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                <h4>Jadwal Ruang</h4>
                <p class="text-muted">Lihat ketersediaan ruang</p>
                <div class="small text-muted mb-3">
                    <ul class="text-start">
                        <li>Cek jadwal penggunaan seluruh ruangan</li>
                        <li>Filter berdasarkan ruangan tertentu</li>
                        <li>Lihat status ketersediaan ruangan</li>
                        <li>Pantau jadwal peminjaman yang aktif</li>
                    </ul>
                </div>
                <a href="{{ route('jadwal.index') }}" class="btn btn-primary">Lihat Jadwal</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="action-card text-center">
                <i class="fas fa-tasks fa-3x mb-3"></i>
                <h4>Peminjaman Saya</h4>
                <p class="text-muted">Lihat riwayat dan status peminjaman Anda</p>
                <div class="small text-muted mb-3">
                    <ul class="text-start">
                        <li>Lihat semua peminjaman yang telah diajukan</li>
                        <li>Pantau status persetujuan peminjaman</li>
                        <li>Batalkan peminjaman jika diperlukan</li>
                        <li>Lihat detail lengkap peminjaman</li>
                    </ul>
                </div>
                <a href="{{ route('peminjam.peminjaman.index') }}" class="btn btn-primary">Lihat Peminjaman</a>
            </div>
        </div>
    </div>
</div>
@endsection
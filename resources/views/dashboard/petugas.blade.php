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

    <div class="row g-4">
        <div class="col-md-6">
            <div class="action-card text-center">
                <i class="fas fa-tasks fa-3x mb-3"></i>
                <h4>Persetujuan Peminjaman</h4>
                <p class="text-muted">{{ $pendingCount }} peminjaman menunggu persetujuan</p>
                <div class="small text-muted mb-3">
                    <ul class="text-start">
                        <li>Review pengajuan peminjaman baru</li>
                        <li>Periksa detail peminjaman</li>
                        <li>Setujui atau tolak permintaan</li>
                        <li>Tambahkan catatan jika diperlukan</li>
                    </ul>
                </div>
                <a href="{{ route('persetujuan.index') }}" class="btn btn-primary">Review Peminjaman</a>
            </div>
        </div>

        <div class="col-md-6">
            <div class="action-card text-center">
                <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                <h4>Jadwal Ruang</h4>
                <p class="text-muted">Kelola jadwal penggunaan ruangan</p>
                <div class="small text-muted mb-3">
                    <ul class="text-start">
                        <li>Monitor penggunaan seluruh ruangan</li>
                        <li>Cek jadwal peminjaman aktif</li>
                        <li>Pantau status setiap ruangan</li>
                        <li>Atur ketersediaan ruangan</li>
                    </ul>
                </div>
                <a href="{{ route('jadwal.index') }}" class="btn btn-primary">Lihat Jadwal</a>
            </div>
        </div>
    </div>
</div>
@endsection
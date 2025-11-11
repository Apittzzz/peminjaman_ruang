@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row g-4">
        <div class="col-md-4">
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

        <div class="col-md-4">
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
                <a href="{{ route('petugas.laporan.index') }}" class="btn btn-primary">Lihat Laporan</a>
            </div>
        </div>
    </div>
</div>
@endsection
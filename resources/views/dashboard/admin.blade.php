@extends('layouts.app')

@section('title', 'Dashboard Admin')



@section('content')
    {{-- Action Cards  --}}
    <div class="row g-4 justify-content-center">
        <div class="col-12 col-md-4">
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
                <div class="d-flex flex-column flex-md-row gap-2 justify-content-center">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary">Kelola User</a>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-outline-secondary">Tambah User</a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
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
                <div class="d-flex flex-column flex-md-row gap-2 justify-content-center">
                    <a href="{{ route('admin.ruang.index') }}" class="btn btn-primary">Kelola</a>
                    <a href="{{ route('admin.ruang.create') }}" class="btn btn-outline-secondary">Tambah Ruang</a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
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
                <div class="d-flex flex-column flex-md-row gap-2 justify-content-center">
                    <a href="{{ route('admin.peminjaman.index') }}" class="btn btn-primary">Kelola</a>
                    <a href="{{ route('persetujuan.index') }}" class="btn btn-outline-secondary">Persetujuan</a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="action-card text-center">
                <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                <h4>Jadwal Ruangan</h4>
                <p class="text-muted">Monitor jadwal penggunaan ruang</p>
                <div class="small text-muted mb-3">
                    <ul class="text-start">
                        <li>Lihat jadwal real-time seluruh ruangan</li>
                        <li>Monitor status ruangan (Kosong/Dipakai)</li>
                        <li>Cek pengguna default ruangan</li>
                        <li>Filter berdasarkan tanggal</li>
                    </ul>
                </div>
                <div class="d-flex flex-column flex-md-row gap-2 justify-content-center">
                    <a href="{{ route('jadwal.index') }}" class="btn btn-primary">Lihat Jadwal</a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
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

<div class="container py-4">
    {{-- Statistik Cards - Responsive: 2 columns on mobile, 4 on desktop --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card bg-primary text-white stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x mb-2 stat-icon"></i>
                    <h3 class="mb-0">{{ $stats['total_users'] }}</h3>
                    <p class="mb-0">Total User</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card bg-success text-white stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-door-open fa-2x mb-2 stat-icon"></i>
                    <h3 class="mb-0">{{ $stats['total_ruang'] }}</h3>
                    <p class="mb-0">Total Ruangan</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card bg-warning text-white stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x mb-2 stat-icon"></i>
                    <h3 class="mb-0">{{ $stats['pending_peminjaman'] }}</h3>
                    <p class="mb-0">Menunggu Persetujuan</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card bg-info text-white stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x mb-2 stat-icon"></i>
                    <h3 class="mb-0">{{ $stats['approved_peminjaman'] }}</h3>
                    <p class="mb-0">Peminjaman Aktif</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Jadwal Hari Ini --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-day"></i> Jadwal Peminjaman Hari Ini
                    </h5>
                </div>
                <div class="card-body">
                    @if($todayBookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Ruangan</th>
                                        <th>Peminjam</th>
                                        <th>Waktu</th>
                                        <th class="d-none d-md-table-cell">Keperluan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayBookings as $booking)
                                    <tr>
                                        <td>
                                            <strong>{{ $booking->ruang->nama_ruang }}</strong>
                                            <small class="d-md-none d-block text-muted">
                                                {{ Str::limit($booking->keperluan, 25) }}
                                            </small>
                                        </td>
                                        <td>{{ $booking->user->nama }}</td>
                                        <td>
                                            <small>{{ $booking->waktu_mulai }}</small><br class="d-md-none">
                                            <small>{{ $booking->waktu_selesai }}</small>
                                        </td>
                                        <td class="d-none d-md-table-cell">{{ Str::limit($booking->keperluan, 30) }}</td>
                                        <td><span class="badge bg-success">{{ ucfirst($booking->status) }}</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center text-md-end">
                            <a href="{{ route('jadwal.index') }}" class="btn btn-sm btn-outline-primary">
                                Lihat Semua Jadwal <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-calendar-times fa-3x mb-3"></i>
                            <p>Tidak ada peminjaman hari ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
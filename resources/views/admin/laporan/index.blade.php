@extends('layouts.app')

@section('title', 'Laporan Peminjaman Ruangan')

@section('breadcrumb')
<head> 
    <link rel="stylesheet" href="{{ asset('css/style.css') }}"> 
    </head>

<li class="breadcrumb-item active" aria-current="page">
    <i class="fas fa-file-alt"></i> Laporan Peminjaman
</li>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Filter Periode -->
    <div class="card laporan-card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Laporan</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.laporan.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="periode" class="form-label fw-bold">Pilih Periode:</label>
                    <select name="periode" id="periode" class="form-select" onchange="this.form.submit()">
                        <option value="hari_ini" {{ $periode == 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="minggu_ini" {{ $periode == 'minggu_ini' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="bulan_ini" {{ $periode == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="tahun_ini" {{ $periode == 'tahun_ini' ? 'selected' : '' }}>Tahun Ini</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Periode Laporan:</label>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Export Laporan:</label>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.laporan.export', ['periode' => $periode, 'format' => 'excel']) }}" class="btn btn-success flex-fill">
                            <i class="fas fa-file-excel me-2"></i>Excel
                        </a>
                        <a href="{{ route('admin.laporan.export', ['periode' => $periode, 'format' => 'csv']) }}" class="btn btn-info flex-fill">
                            <i class="fas fa-file-csv me-2"></i>CSV
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row">
        <div class="col-md-2">
            <div class="card stat-card total">
                <div class="card-body text-center">
                    <i class="fas fa-clipboard-list stat-icon text-primary"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['total_peminjaman'] }}</h3>
                    <small class="text-muted text-light">Total Peminjaman</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card pending">
                <div class="card-body text-center">
                    <i class="fas fa-clock stat-icon text-warning"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['pending'] }}</h3>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card approved">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle stat-icon text-success"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['approved'] }}</h3>
                    <small class="text-muted">Approved</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card rejected">
                <div class="card-body text-center">
                    <i class="fas fa-times-circle stat-icon text-danger"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['rejected'] }}</h3>
                    <small class="text-muted">Rejected</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card selesai">
                <div class="card-body text-center">
                    <i class="fas fa-check-double stat-icon text-secondary"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['selesai'] }}</h3>
                    <small class="text-muted">Selesai</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card cancelled">
                <div class="card-body text-center">
                    <i class="fas fa-ban stat-icon text-secondary"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['cancelled'] }}</h3>
                    <small class="text-muted">Cancelled</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Top 5 Ruangan & User -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-door-open me-2"></i>Top 5 Ruangan Terpopuler</h6>
                </div>
                <div class="card-body">
                    @if($ruangStats->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($ruangStats as $index => $stat)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                    <strong>{{ $stat->ruang->nama_ruang ?? '-' }}</strong>
                                </div>
                                <span class="badge bg-info rounded-pill">{{ $stat->total }} peminjaman</span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">Belum ada data</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-users me-2"></i>Top 5 Peminjam Teraktif</h6>
                </div>
                <div class="card-body">
                    @if($userStats->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($userStats as $index => $stat)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                    <strong>{{ $stat->user->nama ?? '-' }}</strong>
                                </div>
                                <span class="badge bg-success rounded-pill">{{ $stat->total }} peminjaman</span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">Belum ada data</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Detail Peminjaman -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Detail Peminjaman</h5>
        </div>
        <div class="card-body">
            @if($peminjaman->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Peminjam</th>
                                <th>Ruangan</th>
                                <th>Tanggal Pinjam</th>
                                <th>Waktu</th>
                                <th>Keperluan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($peminjaman as $index => $p)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $p->user->nama ?? '-' }}</td>
                                <td>{{ $p->ruang->nama_ruang ?? '-' }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($p->tanggal_pinjam)->format('d/m/Y') }}
                                    @if($p->tanggal_pinjam != $p->tanggal_kembali)
                                        - {{ \Carbon\Carbon::parse($p->tanggal_kembali)->format('d/m/Y') }}
                                    @endif
                                </td>
                                <td>{{ $p->waktu_mulai }} - {{ $p->waktu_selesai }}</td>
                                <td>
                                    <small>{{ \Illuminate\Support\Str::limit($p->keperluan, 50) }}</small>
                                </td>
                                <td>
                                    @if($p->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($p->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($p->status == 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @elseif($p->status == 'selesai')
                                        <span class="badge bg-secondary">Selesai</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($p->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>Tidak ada data peminjaman pada periode ini.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

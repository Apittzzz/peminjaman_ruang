@extends('layouts.app')

@section('title', 'Laporan Peminjaman Ruangan')

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">
    <i class="fas fa-file-alt"></i> Laporan Peminjaman
</li>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Filter Periode -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Laporan</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.laporan.index') }}" class="row g-3">
                <div class="col-12 col-md-4">
                    <label for="periode" class="form-label fw-bold">Pilih Periode:</label>
                    <select name="periode" id="periode" class="form-select" onchange="this.form.submit()">
                        <option value="hari_ini" {{ $periode == 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="minggu_ini" {{ $periode == 'minggu_ini' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="bulan_ini" {{ $periode == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="tahun_ini" {{ $periode == 'tahun_ini' ? 'selected' : '' }}>Tahun Ini</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-bold">Periode Laporan:</label>
                    <div class="alert alert-info mb-0 py-2">
                        <i class="fas fa-calendar-alt me-2"></i>
                        <small class="fw-semibold">{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</small>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-bold">Export Laporan:</label>
                    <div class="d-grid">
                        <a href="{{ route('admin.laporan.export', ['periode' => $periode, 'format' => 'excel']) }}" class="btn btn-success">
                            <i class="fas fa-file-excel me-2"></i>Export ke Excel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistik Cards - Responsive: 2 per row on mobile, 3 on tablet, 6 on desktop -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-sm-4 col-md-2">
            <div class="card stat-card border-primary shadow-sm h-100">
                <div class="card-body text-center p-3">
                    <i class="fas fa-clipboard-list fs-2 text-primary mb-2"></i>
                    <h3 class="mt-2 mb-1 fw-bold">{{ $stats['total_peminjaman'] }}</h3>
                    <small class="text-muted d-block">Total Peminjaman</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
            <div class="card stat-card border-warning shadow-sm h-100">
                <div class="card-body text-center p-3">
                    <i class="fas fa-clock fs-2 text-warning mb-2"></i>
                    <h3 class="mt-2 mb-1 fw-bold">{{ $stats['pending'] }}</h3>
                    <small class="text-muted d-block">Pending</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
            <div class="card stat-card border-success shadow-sm h-100">
                <div class="card-body text-center p-3">
                    <i class="fas fa-check-circle fs-2 text-success mb-2"></i>
                    <h3 class="mt-2 mb-1 fw-bold">{{ $stats['approved'] }}</h3>
                    <small class="text-muted d-block">Approved</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
            <div class="card stat-card border-danger shadow-sm h-100">
                <div class="card-body text-center p-3">
                    <i class="fas fa-times-circle fs-2 text-danger mb-2"></i>
                    <h3 class="mt-2 mb-1 fw-bold">{{ $stats['rejected'] }}</h3>
                    <small class="text-muted d-block">Rejected</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
            <div class="card stat-card border-info shadow-sm h-100">
                <div class="card-body text-center p-3">
                    <i class="fas fa-check-double fs-2 text-info mb-2"></i>
                    <h3 class="mt-2 mb-1 fw-bold">{{ $stats['selesai'] }}</h3>
                    <small class="text-muted d-block">Selesai</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
            <div class="card stat-card border-secondary shadow-sm h-100">
                <div class="card-body text-center p-3">
                    <i class="fas fa-ban fs-2 text-secondary mb-2"></i>
                    <h3 class="mt-2 mb-1 fw-bold">{{ $stats['cancelled'] }}</h3>
                    <small class="text-muted d-block">Cancelled</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Detail Peminjaman -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Detail Peminjaman</h5>
        </div>
        <div class="card-body p-0">
            @if($peminjaman->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="d-none d-md-table-cell text-center" style="width: 50px;">No</th>
                                <th class="d-none d-lg-table-cell">Tanggal Pengajuan</th>
                                <th>Peminjam</th>
                                <th class="d-none d-lg-table-cell">Ruangan</th>
                                <th class="d-none d-md-table-cell">Tanggal Pinjam</th>
                                <th class="d-none d-lg-table-cell">Waktu</th>
                                <th class="d-none d-xl-table-cell">Keperluan</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($peminjaman as $index => $p)
                            <tr>
                                <td class="d-none d-md-table-cell text-center align-middle">{{ $index + 1 }}</td>
                                <td class="d-none d-lg-table-cell align-middle">
                                    <small>{{ $p->created_at->format('d/m/Y') }}</small><br>
                                    <small class="text-muted">{{ $p->created_at->format('H:i') }}</small>
                                </td>
                                <td class="align-middle">
                                    <strong class="d-block">{{ $p->user->nama ?? '-' }}</strong>
                                    
                                    <!-- Mobile View: Show all info under name -->
                                    <div class="d-lg-none mt-2">
                                        <div class="small text-muted mb-1">
                                            <i class="fas fa-door-open text-primary me-1"></i> 
                                            <span class="fw-semibold">{{ $p->ruang->nama_ruang ?? '-' }}</span>
                                        </div>
                                        <div class="small text-muted mb-1">
                                            <i class="fas fa-calendar text-success me-1"></i> 
                                            {{ \Carbon\Carbon::parse($p->tanggal_pinjam)->format('d/m/Y') }}
                                            @if($p->tanggal_pinjam != $p->tanggal_kembali)
                                                - {{ \Carbon\Carbon::parse($p->tanggal_kembali)->format('d/m/Y') }}
                                            @endif
                                        </div>
                                        <div class="small text-muted">
                                            <i class="fas fa-clock text-warning me-1"></i> 
                                            {{ $p->waktu_mulai }} - {{ $p->waktu_selesai }}
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-lg-table-cell align-middle">
                                    <span class="fw-semibold">{{ $p->ruang->nama_ruang ?? '-' }}</span>
                                </td>
                                <td class="d-none d-md-table-cell align-middle">
                                    <small>{{ \Carbon\Carbon::parse($p->tanggal_pinjam)->format('d/m/Y') }}</small>
                                    @if($p->tanggal_pinjam != $p->tanggal_kembali)
                                        <br><small class="text-muted">s/d {{ \Carbon\Carbon::parse($p->tanggal_kembali)->format('d/m/Y') }}</small>
                                    @endif
                                </td>
                                <td class="d-none d-lg-table-cell align-middle">
                                    <small>{{ $p->waktu_mulai }} - {{ $p->waktu_selesai }}</small>
                                </td>
                                <td class="d-none d-xl-table-cell align-middle">
                                    <small class="text-muted">{{ \Illuminate\Support\Str::limit($p->keperluan, 40) }}</small>
                                </td>
                                <td class="text-center align-middle">
                                    @if($p->status == 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($p->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($p->status == 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @elseif($p->status == 'selesai')
                                        <span class="badge bg-info">Selesai</span>
                                    @elseif($p->status == 'cancelled')
                                        <span class="badge bg-secondary">Cancelled</span>
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
                <div class="alert alert-info text-center m-3">
                    <i class="fas fa-info-circle me-2"></i>Tidak ada data peminjaman pada periode ini.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

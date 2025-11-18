@extends('layouts.app')

@section('title', 'Peminjaman Saya')

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">
    <i class="fas fa-calendar-alt"></i> Peminjaman Saya
</li>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <h4 class="text-dark mb-2 mb-md-0"><i class="fas fa-calendar-alt"></i> Peminjaman Saya</h4>
                <div class="d-flex flex-column flex-md-row gap-2 ms-md-auto">
                    <a href="{{ route('peminjam.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                    </a>
                    <a href="{{ route('peminjam.peminjaman.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Ajukan Peminjaman
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($peminjaman->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th class="d-none d-md-table-cell">#</th>
                                    <th>Ruangan</th>
                                    <th class="d-none d-md-table-cell">Tanggal Mulai</th>
                                    <th class="d-none d-md-table-cell">Tanggal Selesai</th>
                                    <th class="d-none d-lg-table-cell">Waktu</th>
                                    <th class="d-none d-lg-table-cell">Keperluan</th>
                                    <th>Status</th>
                                    <th class="d-none d-md-table-cell">Diajukan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($peminjaman as $item)
                                <tr>
                                    <td class="d-none d-md-table-cell">{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $item->ruang->nama_ruang }}</strong>
                                        <br>
                                        <small class="text-muted">Kapasitas: {{ $item->ruang->kapasitas }} orang</small>
                                        {{-- Info mobile --}}
                                        <div class="d-md-none mt-2">
                                            <small class="d-block">
                                                <i class="fas fa-calendar"></i> 
                                                {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }} - 
                                                {{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') }}
                                            </small>
                                            <small class="d-block">
                                                <i class="fas fa-clock"></i> 
                                                {{ $item->waktu_mulai }} - {{ $item->waktu_selesai }}
                                            </small>
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</td>
                                    <td class="d-none d-md-table-cell">{{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') }}</td>
                                    <td class="d-none d-lg-table-cell">
                                        <span class="badge bg-primary">{{ $item->waktu_mulai }}</span>
                                        <br>
                                        <span class="badge bg-secondary">{{ $item->waktu_selesai }}</span>
                                    </td>
                                    <td class="d-none d-lg-table-cell">{{ Str::limit($item->keperluan, 50) }}</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                'cancelled' => 'secondary'
                                            ];
                                            $statusText = [
                                                'pending' => 'Menunggu',
                                                'approved' => 'Disetujui',
                                                'rejected' => 'Ditolak',
                                                'cancelled' => 'Dibatalkan'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$item->status] ?? 'secondary' }}">
                                            {{ $statusText[$item->status] ?? ucfirst($item->status) }}
                                        </span>

                                    </td>
                                    <td class="d-none d-md-table-cell">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex flex-column flex-md-row gap-1">
                                            <a href="{{ route('peminjam.peminjaman.show', $item->id_peminjaman) }}" 
                                               class="btn btn-info btn-sm" title="Lihat Detail">
                                                <i class="fas fa-eye"></i><span class="d-md-none ms-1">Detail</span>
                                            </a>
                                            @if($item->status == 'pending')
                                            <form action="{{ route('peminjam.peminjaman.cancel', $item->id_peminjaman) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm w-100" 
                                                        onclick="return confirm('Batalkan peminjaman ini?')"
                                                        title="Batalkan Peminjaman">
                                                    <i class="fas fa-times"></i><span class="d-md-none ms-1">Batalkan</span>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted text-white">Belum ada peminjaman</h5>
                        <p class="text-muted">Mulai dengan mengajukan peminjaman ruangan pertama Anda</p>
                        <a href="{{ route('peminjam.peminjaman.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Ajukan Peminjaman Pertama
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Peminjaman -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white stats-card">
                <div class="card-body text-center">
                    <h5 class="card-title">Total</h5>
                    <h3>{{ $peminjaman->count() }}</h3>
                    <p class="card-text">Semua Peminjaman</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white stats-card">
                <div class="card-body text-center">
                    <h5 class="card-title">Menunggu</h5>
                    <h3>{{ $peminjaman->where('status', 'pending')->count() }}</h3>
                    <p class="card-text">Menunggu Persetujuan</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white stats-card">
                <div class="card-body text-center">
                    <h5 class="card-title">Disetujui</h5>
                    <h3>{{ $peminjaman->where('status', 'approved')->count() }}</h3>
                    <p class="card-text">Peminjaman Aktif</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white stats-card">
                <div class="card-body text-center">
                    <h5 class="card-title">Selesai</h5>
                    <h3>{{ $peminjaman->whereIn('status', ['rejected', 'cancelled'])->count() }}</h3>
                    <p class="card-text">Peminjaman Selesai</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
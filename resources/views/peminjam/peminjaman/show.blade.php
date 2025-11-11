@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('peminjam.peminjaman.index') }}">Peminjaman Saya</a>
</li>
<li class="breadcrumb-item active">Detail Peminjaman</li>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title"><i class="fas fa-info-circle"></i> Detail Peminjaman</h5>
                    <div>
                        <a href="{{ route('peminjam.peminjaman.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $statusColors = [
                            'pending' => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                            'cancelled' => 'secondary'
                        ];
                    @endphp

                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Ruangan</th>
                                    <td>{{ $peminjaman->ruang->nama_ruang }}</td>
                                </tr>
                                <tr>
                                    <th>Kapasitas</th>
                                    <td>{{ $peminjaman->ruang->kapasitas }} orang</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Mulai</th>
                                    <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Selesai</th>
                                    <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('d/m/Y') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Waktu</th>
                                    <td>{{ $peminjaman->waktu_mulai }} - {{ $peminjaman->waktu_selesai }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge bg-{{ $statusColors[$peminjaman->status] }}">
                                            {{ ucfirst($peminjaman->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Diajukan Pada</th>
                                    <td>{{ $peminjaman->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Terakhir Update</th>
                                    <td>{{ $peminjaman->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Keperluan</label>
                        <div class="border p-3 rounded bg-light">
                            {{ $peminjaman->keperluan }}
                        </div>
                    </div>

                    @if($peminjaman->catatan)
                    <div class="mb-4">
                        <label class="form-label fw-bold">Catatan dari Admin/Petugas</label>
                        <div class="border p-3 rounded bg-light">
                            {{ $peminjaman->catatan }}
                        </div>
                    </div>
                    @endif

                    @if($peminjaman->status == 'pending')
                    <div class="mt-4">
                        <form action="{{ route('peminjam.peminjaman.cancel', $peminjaman->id_peminjaman) }}" 
                              method="POST" 
                              onsubmit="return confirm('Apakah Anda yakin ingin membatalkan peminjaman ini?')">
                            @csrf
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-times"></i> Batalkan Peminjaman
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-building"></i> Informasi Ruangan</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-door-open fa-3x text-primary mb-2"></i>
                        <h5>{{ $peminjaman->ruang->nama_ruang }}</h5>
                    </div>
                    
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Kapasitas</strong></td>
                            <td>{{ $peminjaman->ruang->kapasitas }} orang</td>
                        </tr>
                        <tr>
                            <td><strong>Status Saat Ini</strong></td>
                            <td>
                                <span class="badge bg-{{ $peminjaman->ruang->status == 'kosong' ? 'success' : 'danger' }}">
                                    {{ $peminjaman->ruang->status }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-history"></i> Status Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item {{ $peminjaman->status == 'pending' ? 'active' : '' }}">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <small>Diajukan</small>
                                <p class="mb-0">{{ $peminjaman->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        
                        @if(in_array($peminjaman->status, ['approved', 'rejected']))
                        <div class="timeline-item active">
                            <div class="timeline-marker bg-{{ $peminjaman->status == 'approved' ? 'success' : 'danger' }}"></div>
                            <div class="timeline-content">
                                <small>Diproses</small>
                                <p class="mb-0">{{ $peminjaman->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
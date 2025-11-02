@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('admin.persetujuan.index') }}">Persetujuan Peminjaman</a>
</li>
<li class="breadcrumb-item active">Detail Peminjaman</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title"><i class="fas fa-info-circle"></i> Detail Peminjaman</h5>
                    <div>
                        <a href="{{ route('admin.persetujuan.index') }}" class="btn btn-secondary btn-sm">
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
                                    <th width="40%">Peminjam</th>
                                    <td>{{ $peminjaman->user->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Username</th>
                                    <td>{{ $peminjaman->user->username }}</td>
                                </tr>
                                <tr>
                                    <th>Role</th>
                                    <td>
                                        <span class="badge bg-{{ $peminjaman->user->role == 'admin' ? 'danger' : ($peminjaman->user->role == 'petugas' ? 'warning' : 'success') }}">
                                            {{ ucfirst($peminjaman->user->role) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ruangan</th>
                                    <td>{{ $peminjaman->ruang->nama_ruang }}</td>
                                </tr>
                                <tr>
                                    <th>Kapasitas</th>
                                    <td>{{ $peminjaman->ruang->kapasitas }} orang</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Tanggal Mulai</th>
                                    <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Selesai</th>
                                    <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Waktu</th>
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
                        <label class="form-label fw-bold">Catatan</label>
                        <div class="border p-3 rounded bg-light">
                            {{ $peminjaman->catatan }}
                        </div>
                    </div>
                    @endif

                    @if($peminjaman->status == 'pending')
                    <div class="mt-4">
                        <div class="btn-group">
                            <button type="button" class="btn btn-success" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#approveModal">
                                <i class="fas fa-check"></i> Setujui
                            </button>
                            <button type="button" class="btn btn-danger" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#rejectModal">
                                <i class="fas fa-times"></i> Tolak
                            </button>
                        </div>
                    </div>

                    <!-- Approve Modal -->
                    <div class="modal fade" id="approveModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title">Setujui Peminjaman</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('admin.persetujuan.approve', $peminjaman->id_peminjaman) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <p>Apakah Anda yakin ingin menyetujui peminjaman ini?</p>
                                        <div class="mb-3">
                                            <label for="catatan" class="form-label">Catatan (Opsional)</label>
                                            <textarea class="form-control" id="catatan" 
                                                      name="catatan" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-success">Setujui</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Reject Modal -->
                    <div class="modal fade" id="rejectModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">Tolak Peminjaman</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('admin.persetujuan.reject', $peminjaman->id_peminjaman) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <p>Apakah Anda yakin ingin menolak peminjaman ini?</p>
                                        <div class="mb-3">
                                            <label for="catatan_reject" class="form-label">Alasan Penolakan *</label>
                                            <textarea class="form-control" id="catatan_reject" 
                                                      name="catatan" rows="3" placeholder="Berikan alasan penolakan..." required></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-danger">Tolak</button>
                                    </div>
                                </form>
                            </div>
                        </div>
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
                    <h6 class="mb-0"><i class="fas fa-user"></i> Informasi Peminjam</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-user-circle fa-3x text-info mb-2"></i>
                        <h5>{{ $peminjaman->user->nama }}</h5>
                    </div>
                    
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Username</strong></td>
                            <td>{{ $peminjaman->user->username }}</td>
                        </tr>
                        <tr>
                            <td><strong>Role</strong></td>
                            <td>
                                <span class="badge bg-{{ $peminjaman->user->role == 'admin' ? 'danger' : ($peminjaman->user->role == 'petugas' ? 'warning' : 'success') }}">
                                    {{ ucfirst($peminjaman->user->role) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Terdaftar Sejak</strong></td>
                            <td>{{ $peminjaman->user->created_at->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
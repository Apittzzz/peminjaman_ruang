@extends('layouts.app')

@section('title', 'Persetujuan Peminjaman')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header text-center">
            <h4><i class="fas fa-check-circle"></i> Persetujuan Peminjaman</h4>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Pending Peminjaman -->
            <div class="mb-4">
                <h5 class="text-center text-beige"><i class="fas fa-clock"></i> Menunggu Persetujuan</h5>
                @if($peminjaman->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="d-none d-md-table-cell">#</th>
                                <th>Peminjam</th>
                                <th class="d-none d-lg-table-cell">Ruangan</th>
                                <th class="d-none d-md-table-cell">Tanggal</th>
                                <th class="d-none d-lg-table-cell">Waktu</th>
                                <th class="d-none d-lg-table-cell">Keperluan</th>
                                <th class="d-none d-lg-table-cell">Diajukan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($peminjaman as $item)
                            <tr>
                                <td class="d-none d-md-table-cell">{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $item->user->nama }}</strong>
                                    <br>
                                    <small class="text-muted">@ {{ $item->user->username }}</small>
                                    <div class="d-lg-none small text-muted mt-1">
                                        <div><i class="fas fa-door-open"></i> {{ $item->ruang->nama_ruang }}</div>
                                        <div><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</div>
                                        <div><i class="fas fa-clock"></i> {{ $item->waktu_mulai }} - {{ $item->waktu_selesai }}</div>
                                    </div>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <strong>{{ $item->ruang->nama_ruang }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $item->ruang->kapasitas }} orang</small>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}
                                    @if($item->tanggal_pinjam != $item->tanggal_kembali)
                                        <br>
                                        <small class="text-muted">s/d {{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') }}</small>
                                    @endif
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <span class="badge bg-primary">{{ $item->waktu_mulai }}</span>
                                    <br>
                                    <span class="badge bg-secondary">{{ $item->waktu_selesai }}</span>
                                </td>
                                <td class="d-none d-lg-table-cell">{{ Str::limit($item->keperluan, 50) }}</td>
                                <td class="d-none d-lg-table-cell">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.persetujuan.show', $item->id_peminjaman) }}" 
                                           class="btn btn-info btn-sm" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-success btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#approveModal{{ $item->id_peminjaman }}"
                                                title="Setujui">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#rejectModal{{ $item->id_peminjaman }}"
                                                title="Tolak">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>

                                    <!-- Approve Modal -->
                                    <div class="modal fade" id="approveModal{{ $item->id_peminjaman }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-success text-white">
                                                    <h5 class="modal-title">Setujui Peminjaman</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('admin.persetujuan.approve', $item->id_peminjaman) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Apakah Anda yakin ingin menyetujui peminjaman ini?</p>
                                                        <div class="mb-3">
                                                            <label for="catatan{{ $item->id_peminjaman }}" class="form-label">Catatan (Opsional)</label>
                                                            <textarea class="form-control" id="catatan{{ $item->id_peminjaman }}" 
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
                                    <div class="modal fade" id="rejectModal{{ $item->id_peminjaman }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">Tolak Peminjaman</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('admin.persetujuan.reject', $item->id_peminjaman) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Apakah Anda yakin ingin menolak peminjaman ini?</p>
                                                        <div class="mb-3">
                                                            <label for="catatan_reject{{ $item->id_peminjaman }}" class="form-label">Alasan Penolakan *</label>
                                                            <textarea class="form-control" id="catatan_reject{{ $item->id_peminjaman }}" 
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
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5 class="text-success">Tidak ada peminjaman yang menunggu persetujuan</h5>
                    <p class="text-muted">Semua peminjaman telah diproses</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
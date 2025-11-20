@extends('layouts.app')

        @section('title', 'Persetujuan Peminjaman')

        @section('breadcrumb')
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-check-circle"></i> Persetujuan Peminjaman
        </li>
        @endsection

        @section('content')
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center text">
                        <h4><i class="fas fa-check-circle"></i> Persetujuan Peminjaman</h4>
                        <div>
                            <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('petugas.dashboard') }}" 
                            class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Simple Table -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-clock"></i> Menunggu Persetujuan ({{ $peminjaman->count() }})</h5>
                        </div>
                        <div class="card-body">
                            @if($peminjaman->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead style="background-color: white; color: black;">
                                        <tr>
                                            <th class="d-none d-md-table-cell">#</th>
                                            <th>Peminjam</th>
                                            <th class="d-none d-lg-table-cell">Ruangan</th>
                                            <th class="d-none d-md-table-cell">Tanggal</th>
                                            <th class="d-none d-lg-table-cell">Waktu</th>
                                            <th class="d-none d-lg-table-cell">Keperluan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($peminjaman as $item)
                                        <tr>
                                            <td class="d-none d-md-table-cell">{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $item->user->nama }}</strong>
                                                <div class="d-md-none small text-muted">
                                                    <div><i class="fas fa-door-open"></i> {{ $item->ruang->nama_ruang }}</div>
                                                    <div><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</div>
                                                    <div><i class="fas fa-clock"></i> {{ $item->waktu_mulai }} - {{ $item->waktu_selesai }}</div>
                                                </div>
                                            </td>
                                            <td class="d-none d-lg-table-cell">{{ $item->ruang->nama_ruang }}</td>
                                            <td class="d-none d-md-table-cell">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</td>
                                            <td class="d-none d-lg-table-cell">{{ $item->waktu_mulai }} - {{ $item->waktu_selesai }}</td>
                                            <td class="d-none d-lg-table-cell">{{ Str::limit($item->keperluan, 50) }}</td>
                                            <td>
                                                <form action="{{ route('persetujuan.approve', $item->id_peminjaman) }}" 
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm" 
                                                            onclick="return confirm('Setujui peminjaman ini?')">
                                                        <i class="fas fa-check"></i><span class="d-none d-lg-inline"> Setujui</span>
                                                    </button>
                                                </form>

                                                <button type="button" class="btn btn-danger btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#rejectModal{{ $item->id_peminjaman }}">
                                                    <i class="fas fa-times"></i><span class="d-none d-lg-inline"> Tolak</span>
                                                </button>

                                                <!-- Reject Modal -->
                                                <div class="modal fade" id="rejectModal{{ $item->id_peminjaman }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger text-white">
                                                                <h5 class="modal-title">Tolak Peminjaman</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form action="{{ route('persetujuan.reject', $item->id_peminjaman) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Alasan Penolakan *</label>
                                                                        <textarea class="form-control" name="catatan" rows="3" required></textarea>
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
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
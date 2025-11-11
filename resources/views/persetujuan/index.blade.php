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
                        <div class="card-header bg-warning text-white">
                            <h5 class="mb-0"><i class="fas fa-clock"></i> Menunggu Persetujuan ({{ $peminjaman->count() }})</h5>
                        </div>
                        <div class="card-body">
                            @if($peminjaman->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Peminjam</th>
                                            <th>Ruangan</th>
                                            <th>Tanggal</th>
                                            <th>Waktu</th>
                                            <th>Keperluan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($peminjaman as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->user->nama }}</td>
                                            <td>{{ $item->ruang->nama_ruang }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</td>
                                            <td>{{ $item->waktu_mulai }} - {{ $item->waktu_selesai }}</td>
                                            <td>{{ Str::limit($item->keperluan, 50) }}</td>
                                            <td>
                                                <form action="{{ route('persetujuan.approve', $item->id_peminjaman) }}" 
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm" 
                                                            onclick="return confirm('Setujui peminjaman ini?')">
                                                        <i class="fas fa-check"></i> Setujui
                                                    </button>
                                                </form>

                                                <!-- allow petugas to manually mark as finished -->
                                                @if(Auth::user()->role === 'petugas')
                                                    <form action="{{ route('petugas.peminjaman.complete', $item->id_peminjaman) }}" method="POST" class="d-inline ms-1">
                                                        @csrf
                                                        <button type="submit" class="btn btn-info btn-sm" onclick="return confirm('Tandai peminjaman ini selesai?')">
                                                            <i class="fas fa-flag-checkered"></i> Selesai
                                                        </button>
                                                    </form>
                                                @endif

                                                <button type="button" class="btn btn-danger btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#rejectModal{{ $item->id_peminjaman }}">
                                                    <i class="fas fa-times"></i> Tolak
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
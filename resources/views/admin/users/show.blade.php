@extends('layouts.app')

@section('title', 'Detail User')

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('admin.users.index') }}">Manajemen User</a>
</li>
<li class="breadcrumb-item active">Detail User: {{ $user->nama }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">Detail User: {{ $user->nama }}</h5>
                <div>
                    <a href="{{ route('admin.users.edit', $user->id_user) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">ID User</th>
                                <td>{{ $user->id_user }}</td>
                            </tr>
                            <tr>
                                <th>Username</th>
                                <td>{{ $user->username }}</td>
                            </tr>
                            <tr>
                                <th>Nama Lengkap</th>
                                <td>{{ $user->nama }}</td>
                            </tr>
                            <tr>
                                <th>Role</th>
                                <td>
                                    <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'petugas' ? 'warning' : 'success') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Tanggal Dibuat</th>
                                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Terakhir Diupdate</th>
                                <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-history"></i> Aktivitas Terkait</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Total Peminjaman:</strong> {{ $user->peminjaman->count() }}</p>
                                <p><strong>Total Laporan:</strong> {{ $user->laporan->count() }}</p>
                                
                                @if($user->peminjaman->count() > 0)
                                    <hr>
                                    <h6>5 Peminjaman Terbaru:</h6>
                                    <ul class="list-group list-group-flush">
                                        @foreach($user->peminjaman->take(5) as $peminjaman)
                                            <li class="list-group-item">
                                                <small>{{ $peminjaman->ruang->nama_ruang }} - {{ $peminjaman->tanggal_pinjam }}</small>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delete Button (with confirmation) -->
                <div class="mt-4">
                    <form action="{{ route('admin.users.destroy', $user->id_user) }}" method="POST" 
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                                {{ $user->id_user === auth()->id() ? 'disabled' : '' }}>
                            <i class="fas fa-trash"></i> Hapus User
                        </button>
                        @if($user->id_user === auth()->id())
                            <small class="text-muted d-block mt-1">Tidak dapat menghapus akun sendiri</small>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
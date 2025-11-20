@extends('layouts.app')

@section('title', 'Manajemen User')

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">
    <i class="fas fa-users"></i> Manajemen User
</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <h4 class="text-dark mb-2 mb-md-0"><i class="fas fa-users"></i> Manajemen User</h4>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i><span class="d-none d-sm-inline"> Kembali</span>
                    </a>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i><span class="d-none d-sm-inline"> Tambah User</span>
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
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th class="d-none d-md-table-cell">#</th>
                                    <th>Username</th>
                                    <th class="d-none d-lg-table-cell">Nama</th>
                                    <th>Role</th>
                                    <th class="d-none d-lg-table-cell">Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td class="d-none d-md-table-cell">{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $user->username }}</strong>
                                        @if($user->id_user === auth()->id())
                                            <span class="badge bg-info">Anda</span>
                                        @endif
                                        <div class="d-lg-none small text-muted">
                                            <div>{{ $user->nama }}</div>
                                            <div><i class="fas fa-calendar"></i> {{ $user->created_at->format('d/m/Y') }}</div>
                                        </div>
                                    </td>
                                    <td class="d-none d-lg-table-cell">{{ $user->nama }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'petugas' ? 'warning' : 'success') }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="d-none d-lg-table-cell">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.users.show', $user->id_user) }}" class="btn btn-info btn-sm" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user->id_user) }}" class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                        <form action="{{ route('admin.users.destroy', $user->id_user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus user {{ $user->nama }}?')"
                                                {{ $user->id_user === auth()->id() ? 'disabled' : '' }}
                                                title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Tidak ada data user.</p>
                                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Tambah User Pertama
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!--  Pagination (if needed in the future) --> 
                    @if(method_exists($users, 'links'))
                    <div class="d-flex justify-content-center mt-3">
                        {{ $users->links() }}
                    </div>
                    @endif 
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
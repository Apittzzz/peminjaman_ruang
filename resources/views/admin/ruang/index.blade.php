@extends('layouts.app')

@section('title', 'Manajemen Ruang')

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">
    <i class="fas fa-door-open"></i> Manajemen Ruang
</li>
@endsection


@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Manajemen Ruang</h5>
                    <a href="{{ route('admin.ruang.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Ruang
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Ruang</th>
                                    <th>Kapasitas</th>
                                    <th>Status</th>
                                    <th>Pengguna Default</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ruangs as $ruang)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $ruang->nama_ruang }}</td>
                                    <td>{{ $ruang->kapasitas }} orang</td>
                                    <td>
                                        <span class="badge bg-{{ $ruang->status == 'kosong' ? 'success' : 'danger' }}">
                                            {{ $ruang->status }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($ruang->pengguna_default)
                                            {{ $ruang->pengguna_default }}
                                            @if($ruang->keterangan_penggunaan)
                                                <br><small class="text-muted">{{ $ruang->keterangan_penggunaan }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.ruang.show', $ruang->id_ruang) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.ruang.edit', $ruang->id_ruang) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.ruang.destroy', $ruang->id_ruang) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus ruang ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data ruang.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
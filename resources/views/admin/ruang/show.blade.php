@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Detail Ruang</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Nama Ruang</th>
                                    <td>{{ $ruang->nama_ruang }}</td>
                                </tr>
                                <tr>
                                    <th>Kapasitas</th>
                                    <td>{{ $ruang->kapasitas }} orang</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge bg-{{ $ruang->status == 'kosong' ? 'success' : 'danger' }}">
                                            {{ $ruang->status }}
                                        </span>
                                    </td>
                                </tr>
                                @if($ruang->pengguna_default)
                                <tr>
                                    <th>Pengguna Default</th>
                                    <td>{{ $ruang->pengguna_default }}</td>
                                </tr>
                                @endif
                                @if($ruang->keterangan_penggunaan)
                                <tr>
                                    <th>Keterangan Penggunaan</th>
                                    <td>{{ $ruang->keterangan_penggunaan }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Tanggal Dibuat</th>
                                    <td>{{ $ruang->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Terakhir Diupdate</th>
                                    <td>{{ $ruang->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                            <a href="{{ route('admin.ruang.edit', $ruang->id_ruang) }}" class="btn btn-warning">Edit</a>
                            <a href="{{ route('admin.ruang.index') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
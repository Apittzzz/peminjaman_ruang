@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Dashboard Petugas</h5>
            </div>
            <div class="card-body">
                <p>Selamat datang di dashboard Petugas. Anda dapat mengelola peminjaman ruang.</p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card text-white bg-warning mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Peminjaman</h5>
                                <p class="card-text">Kelola persetujuan peminjaman ruang</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card text-white bg-info mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Laporan</h5>
                                <p class="card-text">Lihat dan generate laporan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
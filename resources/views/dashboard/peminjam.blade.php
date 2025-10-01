@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Dashboard Peminjam</h5>
            </div>
            <div class="card-body">
                <p>Selamat datang di dashboard Peminjam. Anda dapat mengajukan peminjaman ruang.</p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Ajukan Peminjaman</h5>
                                <p class="card-text">Ajukan permohonan peminjaman ruang</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Jadwal Ruang</h5>
                                <p class="card-text">Lihat jadwal ketersediaan ruang</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
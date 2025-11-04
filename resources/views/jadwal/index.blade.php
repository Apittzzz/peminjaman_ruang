@extends('layouts.app')

@section('title', 'Jadwal Ruangan')

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">
    <i class="fas fa-calendar-alt"></i> Jadwal Ruangan
</li>
@endsection

@section('content')
<div class="container-fluid py-4">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }
        .accordion-button {
            background-color: white;
            font-weight: 500;
            color: #2c3e50;
        }
        .accordion-button:not(.collapsed) {
            background-color: #e8f4fd;
            color: #2c3e50;
        }
        .accordion-body {
            background-color: #ffffff;
        }
    </style>

<form method="GET" action="{{ route('jadwal.index') }}" class="mb-4">
    <div class="row g-2 align-items-center">
        <div class="col-auto">
            <label for="tanggal" class="form-label mb-0">Tanggal:</label>
        </div>
        <div class="col-auto">
            <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $selectedTanggal }}">
        </div>
        <div class="col-auto">
            <font color = "black">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="semua" id="semua" value="1" {{ request('semua') ? 'checked' : '' }}>
                <label class="form-check-label" for="semua">
                    Lihat semua peminjaman
                </label>
            </div>
            </font>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Tampilkan</button>
        </div>
    </div>
</form>

    <!-- Jadwal Ruangan Accordion -->
    <div class="accordion" id="jadwalRuangan">
        @foreach($ruangs as $ruang)
        <div class="accordion-item mb-3">
            <h2 class="accordion-header" id="heading{{ $ruang->id_ruang }}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $ruang->id_ruang }}" aria-expanded="false" aria-controls="collapse{{ $ruang->id_ruang }}">
                    <i class="fas fa-door-open me-2 text-{{ $ruang->status == 'kosong' ? 'success' : 'danger' }}"></i>
                    {{ $ruang->nama_ruang }} —
                    <span class="badge bg-{{ $ruang->status == 'kosong' ? 'success' : 'danger' }} ms-2">
                        {{ ucfirst($ruang->status) }}
                    </span>
                </button>
            </h2>
            <div id="collapse{{ $ruang->id_ruang }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $ruang->id_ruang }}" data-bs-parent="#jadwalRuangan">
                <div class="accordion-body">
                    <p class="text-muted">Kapasitas: {{ $ruang->kapasitas }} orang</p>

                    @if($ruang->peminjaman && $ruang->peminjaman->count() > 0)
                        <ul class="list-group">
                            @foreach($ruang->peminjaman as $p)
                            <li class="list-group-item">
                                <strong>{{ $p->user->name ?? '—' }}</strong><br>
                                {{ \Carbon\Carbon::parse($p->tanggal_pinjam)->format('d/m/Y') }} |
                                {{ $p->waktu_mulai }} - {{ $p->waktu_selesai }}<br>
                                Keperluan: {{ $p->keperluan }}
                            </li>
                            @endforeach
                        </ul>
                        <p class="mt-2 text-muted">Total peminjam: {{ $ruang->peminjaman->count() }}</p>
                    @else
                        <p class="text-muted">Belum ada peminjaman aktif.</p>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

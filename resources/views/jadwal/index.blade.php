@extends('layouts.app')

@section('title', 'Jadwal Ruangan')

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">
    <i class="fas fa-calendar-alt"></i> Jadwal Ruangan
</li>
@endsection

@section('content')
<div class="container-fluid py-4">
<div class="card jadwal-card">
    <div class="card-body">
        <form method="GET" action="{{ route('jadwal.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label for="tanggal" class="form-label fw-bold">Tanggal:</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $selectedTanggal }}">
                </div>
                <!--
                <div class="col-12 col-md-3">
                    <label class="form-label fw-bold">&nbsp;</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="semua" id="semua" value="1" {{ request('semua') ? 'checked' : '' }}>
                        <label class="form-check-label" for="semua">
                            Lihat semua peminjaman
                        </label>
                    </div>
                </div>
                -->    
                <div class="col-12 col-md-4">
                    <label for="status" class="form-label fw-bold">Status Ruangan:</label>
                    <select name="status" id="status" class="form-select">
                        <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="kosong" {{ $statusFilter == 'kosong' ? 'selected' : '' }}>Kosong</option>
                        <option value="dipakai" {{ $statusFilter == 'dipakai' ? 'selected' : '' }}>Dipakai</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i>Tampilkan</button>
                </div>
            </div>
        </form>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

    <!-- Jadwal Ruangan Accordion -->
    <div class="accordion" id="jadwalRuangan">
        @foreach($ruangs as $ruang)
        @php
            $hasActiveBooking = $ruang->peminjaman && $ruang->peminjaman->count() > 0;
            // Prioritas: Jika ada peminjaman aktif = dipakai, jika tidak ada tapi status=dipakai maka tetap dipakai
            $isOccupied = $hasActiveBooking || $ruang->status === 'dipakai';
            $statusColor = $isOccupied ? 'danger' : 'success';
            $statusText = $isOccupied ? 'Dipakai' : 'Kosong';
        @endphp
        <div class="accordion-item mb-3">
            <h2 class="accordion-header" id="heading{{ $ruang->id_ruang }}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $ruang->id_ruang }}" aria-expanded="false" aria-controls="collapse{{ $ruang->id_ruang }}">
                    <i class="fas fa-door-open me-2 text-{{ $statusColor }}"></i>
                    {{ $ruang->nama_ruang }} —
                    <span class="badge bg-{{ $statusColor }} ms-2">
                        {{ $statusText }}
                    </span>
                    @if($ruang->pengguna_default)
                        <small class="text-muted ms-2">({{ $ruang->pengguna_default }})</small>
                    @endif
                    @if($ruang->is_temporary_occupied)
                        <small class="badge bg-warning text-dark ms-2">
                            <i class="fas fa-exchange-alt"></i> Pengguna Sementara: {{ $ruang->pengguna_default_temp }}
                        </small>
                    @endif
                    @if(Auth::user()->role === 'admin')
                        <button type="button" class="btn btn-sm btn-outline-primary ms-auto me-2" data-bs-toggle="modal" data-bs-target="#editPenggunaModal{{ $ruang->id_ruang }}">
                            <i class="fas fa-edit"></i> Edit Pengguna
                        </button>
                    @endif
                </button>
            </h2>
            <div id="collapse{{ $ruang->id_ruang }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $ruang->id_ruang }}" data-bs-parent="#jadwalRuangan">
                <div class="accordion-body">
                    <p class="text-muted mb-3">
                        <i class="fas fa-users"></i> Kapasitas: {{ $ruang->kapasitas }} orang
                        @if($ruang->lokasi)
                            | <i class="fas fa-map-marker-alt"></i> {{ $ruang->lokasi }}
                        @endif
                    </p>

                    {{-- Informasi Pengguna Default --}}
                    @if($ruang->pengguna_default && !$ruang->is_temporary_occupied)
                        <div class="alert alert-info mb-3">
                            <h6 class="alert-heading mb-2">
                                <i class="fas fa-user-tag"></i> Pengguna Default Ruangan
                            </h6>
                            <p class="mb-1"><strong>{{ $ruang->pengguna_default }}</strong></p>
                            @if($ruang->keterangan_penggunaan)
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> {{ $ruang->keterangan_penggunaan }}
                                </small>
                            @else
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> Ruangan ini memiliki pengguna tetap untuk kegiatan reguler
                                </small>
                            @endif
                        </div>
                    @endif

                    {{-- Informasi Temporary Occupant --}}
                    @if($ruang->is_temporary_occupied)
                        <div class="alert alert-warning mb-3">
                            <h6 class="alert-heading mb-2">
                                <i class="fas fa-exchange-alt"></i> Menampung Pengguna Sementara
                            </h6>
                            <p class="mb-1"><strong>{{ $ruang->pengguna_default_temp }}</strong></p>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                @if($ruang->ruangAsal)
                                    Dipindahkan dari: <strong>{{ $ruang->ruangAsal->nama_ruang }}</strong>
                                @endif
                                {{ $ruang->keterangan_penggunaan ? ' - ' . $ruang->keterangan_penggunaan : '' }}
                            </small>
                        </div>
                    @endif

                    {{-- Daftar Peminjaman Aktif --}}
                    @if($ruang->peminjaman && $ruang->peminjaman->count() > 0)
                        <h6 class="mb-2"><i class="fas fa-calendar-check"></i> Peminjaman Aktif:</h6>
                        <ul class="list-group mb-3">
                            @foreach($ruang->peminjaman as $p)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $p->user->nama ?? '—' }}</h6>
                                        <p class="mb-1">
                                            <i class="fas fa-calendar"></i> 
                                            {{ \Carbon\Carbon::parse($p->tanggal_pinjam)->format('d/m/Y') }} - 
                                            {{ \Carbon\Carbon::parse($p->tanggal_kembali)->format('d/m/Y') }}
                                        </p>
                                        <p class="mb-1">
                                            <i class="fas fa-clock"></i> 
                                            {{ $p->waktu_mulai }} - {{ $p->waktu_selesai }}
                                        </p>
                                        <p class="mb-1">
                                            <i class="fas fa-clipboard"></i> 
                                            <strong>Keperluan:</strong> {{ $p->keperluan }}
                                        </p>
                                        @if($p->catatan)
                                            <small class="text-muted">
                                                <i class="fas fa-sticky-note"></i> {{ $p->catatan }}
                                            </small>
                                        @endif
                                    </div>
                                    <span class="badge bg-success">Approved</span>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        <p class="text-muted mb-3">
                            <i class="fas fa-list"></i> Total peminjaman: {{ $ruang->peminjaman->count() }}
                        </p>
                    @else
                        @if(!$ruang->pengguna_default && !$ruang->is_temporary_occupied)
                            <div class="alert alert-success mb-3">
                                <i class="fas fa-check-circle"></i> Tidak ada peminjaman aktif. Ruangan tersedia untuk dipinjam.
                                @if(Auth::user()->role === 'peminjam')
                                    <br><small class="text-muted">Klik tombol "Ajukan Peminjaman" di bawah untuk mengajukan peminjaman ruangan ini.</small>
                                @endif
                            </div>
                        @else
                            <p class="text-muted mb-3">
                                <i class="fas fa-info-circle"></i> Tidak ada peminjaman dari pihak lain saat ini.
                            </p>
                        @endif
                    @endif

                    {{-- Tombol Ajukan Peminjaman untuk Peminjam --}}
                    @if(Auth::user()->role === 'peminjam')
                        <div class="d-grid gap-2">
                            <a href="{{ route('peminjam.peminjaman.create', ['ruang_id' => $ruang->id_ruang]) }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-1"></i> Ajukan Peminjaman Ruangan Ini
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Modal Edit Pengguna Default -->
    @if(Auth::user()->role === 'admin')
        @foreach($ruangs as $ruang)
        <div class="modal fade" id="editPenggunaModal{{ $ruang->id_ruang }}" tabindex="-1" aria-labelledby="editPenggunaModalLabel{{ $ruang->id_ruang }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPenggunaModalLabel{{ $ruang->id_ruang }}">Edit Pengguna Default - {{ $ruang->nama_ruang }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.ruang.update', $ruang->id_ruang) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="status{{ $ruang->id_ruang }}" class="form-label">Status Ruangan</label>
                                <select class="form-select" id="status{{ $ruang->id_ruang }}" name="status" required>
                                    <option value="kosong" {{ $ruang->status == 'kosong' ? 'selected' : '' }}>Kosong</option>
                                    <option value="dipakai" {{ $ruang->status == 'dipakai' ? 'selected' : '' }}>Dipakai</option>
                                </select>
                            </div>
                            <div class="mb-3 pengguna-default-fields" id="penggunaDefaultFields{{ $ruang->id_ruang }}" style="display: {{ $ruang->status == 'dipakai' ? 'block' : 'none' }};">
                                <label for="pengguna_default{{ $ruang->id_ruang }}" class="form-label">Pengguna Default <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="pengguna_default{{ $ruang->id_ruang }}" name="pengguna_default" value="{{ $ruang->pengguna_default }}" placeholder="Contoh: Kelas 10A, Guru Matematika, dll" {{ $ruang->status == 'dipakai' ? 'required' : '' }}>
                                <div class="form-text">Siapa yang secara default menggunakan ruangan ini?</div>
                            </div>
                            <div class="mb-3 pengguna-default-fields" id="keteranganPenggunaanFields{{ $ruang->id_ruang }}" style="display: {{ $ruang->status == 'dipakai' ? 'block' : 'none' }};">
                                <label for="keterangan_penggunaan{{ $ruang->id_ruang }}" class="form-label">Keterangan Penggunaan</label>
                                <textarea class="form-control" id="keterangan_penggunaan{{ $ruang->id_ruang }}" name="keterangan_penggunaan" rows="3" placeholder="Jelaskan penggunaan ruangan ini secara default">{{ $ruang->keterangan_penggunaan }}</textarea>
                                <div class="form-text">Opsional: Jelaskan kegiatan yang biasanya dilakukan di ruangan ini</div>
                            </div>
                            <div class="mb-3">
                                <div class="form-text">
                                    <strong>Catatan:</strong> Perubahan ini akan mempengaruhi status dan pengguna default ruangan.
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    @endif
</div>

<script>
@if(Auth::user()->role === 'admin')
document.addEventListener('DOMContentLoaded', function() {
    // Handle dynamic fields for each modal
    @foreach($ruangs as $ruang)
    (function() {
        const modalId = '{{ $ruang->id_ruang }}';
        const statusSelect = document.getElementById('status' + modalId);
        const penggunaDefaultFields = document.getElementById('penggunaDefaultFields' + modalId);
        const keteranganPenggunaanFields = document.getElementById('keteranganPenggunaanFields' + modalId);
        const penggunaDefaultInput = document.getElementById('pengguna_default' + modalId);

        function togglePenggunaFields() {
            if (statusSelect.value === 'dipakai') {
                penggunaDefaultFields.style.display = 'block';
                keteranganPenggunaanFields.style.display = 'block';
                penggunaDefaultInput.required = true;
            } else {
                penggunaDefaultFields.style.display = 'none';
                keteranganPenggunaanFields.style.display = 'none';
                penggunaDefaultInput.required = false;
                penggunaDefaultInput.value = '';
                document.getElementById('keterangan_penggunaan' + modalId).value = '';
            }
        }

        if (statusSelect) {
            statusSelect.addEventListener('change', togglePenggunaFields);
            // Set initial state
            togglePenggunaFields();
        }
    })();
    @endforeach
});
@endif
</script>
@endsection

@extends('layouts.app')

@section('title', 'Ajukan Peminjaman')

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('peminjam.peminjaman.index') }}">Peminjaman Saya</a>
</li>
<li class="breadcrumb-item active">Ajukan Peminjaman</li>
@endsection

@section('content')
<div class="container-fluid py-4">
    <style>
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }

        .info-card:hover {
            transform: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.08);
        }

        .form-control, .form-select {
            border-radius: 8px;
            padding: 0.8rem;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #2c3e50;
            box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.1);
        }

        .btn {
            border-radius: 8px;
            padding: 0.8rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #2c3e50;
            border: none;
        }

        .btn-primary:hover {
            background: #1a252f;
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 8px;
            border: none;
        }
    </style>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><i class="fas fa-plus"></i> Ajukan Peminjaman Ruang</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('peminjam.peminjaman.store') }}" method="POST" id="peminjamanForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_ruang" class="form-label">Pilih Ruangan *</label>
                                    <select class="form-select @error('id_ruang') is-invalid @enderror" 
                                            id="id_ruang" name="id_ruang" required>
                                        <option value="">-- Pilih Ruangan --</option>
                                        @foreach($ruangs as $ruang)
                                            <option value="{{ $ruang->id_ruang }}" 
                                                {{ old('id_ruang') == $ruang->id_ruang ? 'selected' : '' }}>
                                                {{ $ruang->nama_ruang }} (Kapasitas: {{ $ruang->kapasitas }} orang)
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_ruang')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status Ruangan</label>
                                    <div id="ruang-status">
                                        <span class="badge bg-secondary">Pilih ruangan terlebih dahulu</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_pinjam" class="form-label">Tanggal Mulai *</label>
                                    <input type="date" class="form-control @error('tanggal_pinjam') is-invalid @enderror" 
                                           id="tanggal_pinjam" name="tanggal_pinjam" 
                                           value="{{ old('tanggal_pinjam') }}" 
                                           min="{{ date('Y-m-d') }}" required>
                                    @error('tanggal_pinjam')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_kembali" class="form-label">Tanggal Selesai *</label>
                                    <input type="date" class="form-control @error('tanggal_kembali') is-invalid @enderror" 
                                           id="tanggal_kembali" name="tanggal_kembali" 
                                           value="{{ old('tanggal_kembali') }}" 
                                           min="{{ date('Y-m-d') }}" required>
                                    @error('tanggal_kembali')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="waktu_mulai" class="form-label">Waktu Mulai *</label>
                                    <input type="time" class="form-control @error('waktu_mulai') is-invalid @enderror" 
                                           id="waktu_mulai" name="waktu_mulai" 
                                           value="{{ old('waktu_mulai') }}" required>
                                    @error('waktu_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="invalid-feedback" id="waktu_mulai_error" style="display: none;">Waktu mulai harus diisi</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="waktu_selesai" class="form-label">Waktu Selesai *</label>
                                    <input type="time" class="form-control @error('waktu_selesai') is-invalid @enderror" 
                                           id="waktu_selesai" name="waktu_selesai" 
                                           value="{{ old('waktu_selesai') }}" required>
                                    @error('waktu_selesai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="invalid-feedback" id="waktu_selesai_error" style="display: none;">Waktu selesai harus diisi</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="keperluan" class="form-label">Keperluan *</label>
                            <textarea class="form-control @error('keperluan') is-invalid @enderror" 
                                      id="keperluan" name="keperluan" rows="4" 
                                      placeholder="Jelaskan keperluan peminjaman ruangan..." required>{{ old('keperluan') }}</textarea>
                            @error('keperluan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-label">Maksimal 500 karakter</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('peminjam.peminjaman.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Ajukan Peminjaman
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card info-card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informasi</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-lightbulb"></i> Tips Pengajuan:</h6>
                        <ul class="mb-0 ps-3">
                            <li>Pastikan ruangan tersedia pada tanggal dan waktu yang dipilih</li>
                            <li>Ajukan minimal 1 hari sebelum tanggal peminjaman</li>
                            <li>Isi keperluan dengan jelas dan lengkap</li>
                            <li>Status akan berubah dari <span class="badge bg-warning">Pending</span> menjadi 
                                <span class="badge bg-success">Approved</span> setelah disetujui admin/petugas</li>
                        </ul>
                    </div>
                    
                    <div class="mt-3">
                        <h6>Status Peminjaman:</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-warning">Pending</span>
                            <span class="badge bg-success">Approved</span>
                            <span class="badge bg-danger">Rejected</span>
                            <span class="badge bg-secondary">Cancelled</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ruangSelect = document.getElementById('id_ruang');
    const ruangStatus = document.getElementById('ruang-status');
    const tanggalPinjam = document.getElementById('tanggal_pinjam');
    const tanggalKembali = document.getElementById('tanggal_kembali');
    const waktuMulai = document.getElementById('waktu_mulai');
    const waktuSelesai = document.getElementById('waktu_selesai');
    const waktuMulaiError = document.getElementById('waktu_mulai_error');
    const waktuSelesaiError = document.getElementById('waktu_selesai_error');
    const form = document.getElementById('peminjamanForm');
    
    // Function to validate time
    function validateTime(timeInput, errorDiv) {
        const timeValue = timeInput.value;
        if (!timeValue) {
            timeInput.classList.add('is-invalid');
            errorDiv.style.display = 'block';
            return false;
        } else {
            timeInput.classList.remove('is-invalid');
            errorDiv.style.display = 'none';
            return true;
        }
    }
    
    // Function to validate time range
    function validateTimeRange() {
        const startDate = tanggalPinjam.value;
        const endDate = tanggalKembali.value;
        const startTime = waktuMulai.value;
        const endTime = waktuSelesai.value;
        
        // Skip validation if any field is empty
        if (!startDate || !endDate || !startTime || !endTime) {
            return true;
        }
        
        // If same date, end time must be after start time
        if (startDate === endDate) {
            if (endTime <= startTime) {
                waktuSelesai.classList.add('is-invalid');
                waktuSelesaiError.textContent = 'Waktu selesai harus lebih dari waktu mulai pada hari yang sama';
                waktuSelesaiError.style.display = 'block';
                return false;
            }
        }
        
        // If different dates, any time is valid
        waktuSelesai.classList.remove('is-invalid');
        waktuSelesaiError.style.display = 'none';
        return true;
    }
    
    // Update tanggal_kembali min date when tanggal_pinjam changes
    tanggalPinjam.addEventListener('change', function() {
        tanggalKembali.min = this.value;
        if (tanggalKembali.value && tanggalKembali.value < this.value) {
            tanggalKembali.value = this.value;
        }
        validateTimeRange();
    });
    
    // Update ruang status
    ruangSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const ruangText = selectedOption.text;
            ruangStatus.innerHTML = `<span class="badge bg-success">Tersedia</span>`;
        } else {
            ruangStatus.innerHTML = `<span class="badge bg-secondary">Pilih ruangan terlebih dahulu</span>`;
        }
    });
    
    // Validate tanggal_kembali on change
    tanggalKembali.addEventListener('change', function() {
        validateTimeRange();
    });
    
    // Validate waktu_mulai on change
    waktuMulai.addEventListener('change', function() {
        validateTime(waktuMulai, waktuMulaiError);
        validateTimeRange();
    });
    
    // Validate waktu_selesai on change
    waktuSelesai.addEventListener('change', function() {
        validateTime(waktuSelesai, waktuSelesaiError);
        validateTimeRange();
    });
    
    // Validate on form submit
    form.addEventListener('submit', function(e) {
        const isWaktuMulaiValid = validateTime(waktuMulai, waktuMulaiError);
        const isWaktuSelesaiValid = validateTime(waktuSelesai, waktuSelesaiError);
        const isTimeRangeValid = validateTimeRange();
        
        if (!isWaktuMulaiValid || !isWaktuSelesaiValid || !isTimeRangeValid) {
            e.preventDefault();
            if (!isTimeRangeValid) {
                alert('Pada hari yang sama, waktu selesai harus lebih dari waktu mulai');
            } else {
                alert('Waktu peminjaman harus diisi dengan lengkap');
            }
        }
    });
    
    // Set initial min date for tanggal_kembali
    if (tanggalPinjam.value) {
        tanggalKembali.min = tanggalPinjam.value;
    }
});
</script>
@endsection
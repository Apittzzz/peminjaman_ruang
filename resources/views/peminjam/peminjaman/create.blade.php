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
    <div class="row">
        <div class="col-12 col-lg-8">
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
                            <div class="col-12 col-md-6">
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
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status Ruangan</label>
                                    <div id="ruang-status">
                                        <span class="badge bg-secondary">Pilih ruangan terlebih dahulu</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
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
                            <div class="col-12 col-md-6">
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
                            <div class="col-12 col-md-6">
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
                            <div class="col-12 col-md-6">
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

                        <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
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

        <div class="col-12 col-lg-4">
            {{-- Informasi Ketentuan Peminjaman --}}
            <div class="card info-card mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-clipboard-list"></i> Ketentuan Peminjaman</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-3">
                        <small><i class="fas fa-exclamation-triangle"></i> <strong>Penting!</strong> Harap baca ketentuan berikut sebelum mengajukan peminjaman.</small>
                    </div>
                    
                    <h6 class="text-primary mb-2"> <strong> <i class="fas fa-clock"></i> Waktu Peminjaman</strong></h6>
                    <ul class="small mb-3">
                        <li>Ajukan minimal <strong>1 hari</strong> sebelum tanggal peminjaman</li>
                        <li>Maksimal peminjaman <strong>7 hari berturut-turut</strong></li>
                        <li>Jam operasional: <strong>07:00 - 15:00</strong></li>
                        <li>Pastikan waktu selesai > waktu mulai</li>
                    </ul>

                    <h6 class="text-primary mb-2"> <strong> <i class="fas fa-file-alt"></i> Persyaratan</strong></h6>
                    <ul class="small mb-3">
                        <li>Jelaskan keperluan dengan jelas dan lengkap</li>
                        <li>Sertakan jumlah peserta (jika ada)</li>
                        <li>Pastikan ruangan sesuai kebutuhan</li>
                        <li>Cek jadwal ruangan terlebih dahulu</li>
                    </ul>

                    <h6 class="text-primary mb-2"> <strong> <i class="fas fa-user-tie"></i> Tanggung Jawab </strong></h6>
                    <ul class="small mb-0">
                        <li>Peminjam bertanggung jawab atas kerusakan</li>
                        <li>Wajib mengembalikan ruangan dalam kondisi bersih</li>
                        <li>Matikan semua peralatan elektronik setelah digunakan</li>
                        <li>Laporkan segera jika ada kerusakan</li>
                    </ul> <br>


                    <h6 class="text-primary mb-2"> <strong> <i class="fa-solid fa-check-to-slot"></i> Status Peminajaman</strong></h6>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-warning">Pending</span>
                            <span class="badge bg-success">Approved</span>
                            <span class="badge bg-danger">Rejected</span>
                            <span class="badge bg-secondary">Cancelled</span>
                        </div>
                        <div class="alert alert-info mb-0 py-2">
                            <small><i class="fas fa-lightbulb"></i> Cek status di menu "Peminjaman Saya"</small>
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
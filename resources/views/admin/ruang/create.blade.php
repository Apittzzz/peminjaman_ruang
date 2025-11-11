@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Tambah Ruang Baru</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.ruang.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nama_ruang" class="form-label">Nama Ruang</label>
                            <input type="text" class="form-control @error('nama_ruang') is-invalid @enderror" id="nama_ruang" name="nama_ruang" value="{{ old('nama_ruang') }}" required>
                            @error('nama_ruang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="kapasitas" class="form-label">Kapasitas</label>
                            <input type="number" class="form-control @error('kapasitas') is-invalid @enderror" id="kapasitas" name="kapasitas" value="{{ old('kapasitas') }}" min="1" required>
                            @error('kapasitas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="">Pilih Status</option>
                                <option value="kosong" {{ old('status') == 'kosong' ? 'selected' : '' }}>Kosong</option>
                                <option value="dipakai" {{ old('status') == 'dipakai' ? 'selected' : '' }}>Dipakai</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 pengguna-default-fields" id="penggunaDefaultFields" style="display: none;">
                            <label for="pengguna_default" class="form-label">Pengguna Default <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('pengguna_default') is-invalid @enderror" id="pengguna_default" name="pengguna_default" value="{{ old('pengguna_default') }}" placeholder="Contoh: Kelas 10A, Guru Matematika, dll">
                            <div class="form-text">Siapa yang secara default menggunakan ruangan ini?</div>
                            @error('pengguna_default')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 pengguna-default-fields" id="keteranganPenggunaanFields" style="display: none;">
                            <label for="keterangan_penggunaan" class="form-label">Keterangan Penggunaan</label>
                            <textarea class="form-control @error('keterangan_penggunaan') is-invalid @enderror" id="keterangan_penggunaan" name="keterangan_penggunaan" rows="3" placeholder="Jelaskan penggunaan ruangan ini secara default">{{ old('keterangan_penggunaan') }}</textarea>
                            <div class="form-text">Opsional: Jelaskan kegiatan yang biasanya dilakukan di ruangan ini</div>
                            @error('keterangan_penggunaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.ruang.index') }}" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    const penggunaDefaultFields = document.getElementById('penggunaDefaultFields');
    const keteranganPenggunaanFields = document.getElementById('keteranganPenggunaanFields');
    const penggunaDefaultInput = document.getElementById('pengguna_default');

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
            document.getElementById('keterangan_penggunaan').value = '';
        }
    }

    statusSelect.addEventListener('change', togglePenggunaFields);

    // Check initial state
    togglePenggunaFields();
});
</script>
@endsection
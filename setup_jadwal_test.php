<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ruang;
use App\Models\Peminjaman;
use App\Models\User;
use App\Services\RoomRelocationService;

echo "=== Setup Data Test untuk Halaman Jadwal ===\n\n";

// 1. Buat ruangan dengan pengguna default
echo "1. Membuat ruangan dengan pengguna default...\n";

$ruangKelas = Ruang::firstOrCreate(
    ['nama_ruang' => 'Ruang Kelas 10A'],
    [
        'kapasitas' => 30,
        'status' => 'dipakai',
        'pengguna_default' => 'Kelas 10A - Matematika',
        'keterangan_penggunaan' => 'Ruang kelas reguler untuk Kelas 10A'
    ]
);
echo "   ✓ Ruang '{$ruangKelas->nama_ruang}' (ID: {$ruangKelas->id_ruang})\n";
echo "     Pengguna: {$ruangKelas->pengguna_default}\n";

$ruangKosong1 = Ruang::firstOrCreate(
    ['nama_ruang' => 'Ruang Serbaguna A'],
    [
        'kapasitas' => 40,
        'status' => 'kosong',
        'pengguna_default' => null
    ]
);
echo "   ✓ Ruang '{$ruangKosong1->nama_ruang}' (ID: {$ruangKosong1->id_ruang}) - Kosong\n";

$ruangKosong2 = Ruang::firstOrCreate(
    ['nama_ruang' => 'Ruang Meeting'],
    [
        'kapasitas' => 20,
        'status' => 'kosong',
        'pengguna_default' => null
    ]
);
echo "   ✓ Ruang '{$ruangKosong2->nama_ruang}' (ID: {$ruangKosong2->id_ruang}) - Kosong\n\n";

// 2. Buat peminjaman untuk ruang dengan pengguna default
echo "2. Membuat peminjaman untuk ruang dengan pengguna default...\n";

$peminjam = User::where('role', 'peminjam')->first();
if (!$peminjam) {
    echo "   ✗ Error: Tidak ada user peminjam\n";
    exit(1);
}

$peminjaman = Peminjaman::create([
    'id_user' => $peminjam->id_user,
    'id_ruang' => $ruangKelas->id_ruang,
    'tanggal_pinjam' => now()->format('Y-m-d'),
    'tanggal_kembali' => now()->format('Y-m-d'),
    'waktu_mulai' => '14:00',
    'waktu_selesai' => '16:00',
    'keperluan' => 'Rapat koordinasi guru - Testing relokasi',
    'status' => 'approved'
]);
echo "   ✓ Peminjaman dibuat (ID: {$peminjaman->id_peminjaman})\n";
echo "     Peminjam: {$peminjam->nama}\n";
echo "     Tanggal: {$peminjaman->tanggal_pinjam}\n";
echo "     Waktu: {$peminjaman->waktu_mulai} - {$peminjaman->waktu_selesai}\n\n";

// 3. Lakukan relokasi
echo "3. Melakukan relokasi pengguna default...\n";

$relocationService = new RoomRelocationService();
$result = $relocationService->relocateDefaultUser($peminjaman);

if ($result['success'] && $result['relocated']) {
    echo "   ✓ RELOKASI BERHASIL!\n";
    echo "   ✓ {$result['message']}\n\n";
    
    // Verifikasi data
    $ruangKosongUpdated = Ruang::find($ruangKosong1->id_ruang);
    echo "   Status Ruang Serbaguna A setelah relokasi:\n";
    echo "   - is_temporary_occupied: " . ($ruangKosongUpdated->is_temporary_occupied ? 'true' : 'false') . "\n";
    echo "   - pengguna_default_temp: {$ruangKosongUpdated->pengguna_default_temp}\n";
    echo "   - ruang_asal_id: {$ruangKosongUpdated->ruang_asal_id}\n";
    echo "   - status: {$ruangKosongUpdated->status}\n\n";
} else {
    echo "   ⚠ " . $result['message'] . "\n\n";
}

echo "4. Data siap untuk dilihat di halaman jadwal!\n";
echo "   Akses: http://localhost:8000/jadwal\n";
echo "   Filter tanggal: " . now()->format('Y-m-d') . "\n\n";

echo "=== Untuk membersihkan data test ===\n";
echo "   Hapus peminjaman ID: {$peminjaman->id_peminjaman}\n";
echo "   php artisan tinker --execute=\"App\\Models\\Peminjaman::find({$peminjaman->id_peminjaman})->delete();\"\n\n";

echo "=== Setup Selesai ===\n";

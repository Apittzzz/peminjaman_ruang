<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ruang;
use App\Models\Peminjaman;
use App\Models\User;
use App\Services\RoomRelocationService;

echo "=== Testing Room Relocation System ===\n\n";

// 1. Setup: Buat ruangan dengan pengguna default
echo "1. Membuat ruangan test...\n";

$ruangDenganPengguna = Ruang::create([
    'nama_ruang' => 'Lab Komputer 1',
    'kapasitas' => 30,
    'status' => 'kosong',
    'pengguna_default' => 'Kelas 12A',
    'keterangan_penggunaan' => 'Kelas IT Programming'
]);
echo "   ✓ Ruang '{$ruangDenganPengguna->nama_ruang}' dibuat dengan pengguna default: {$ruangDenganPengguna->pengguna_default}\n";

$ruangKosong = Ruang::create([
    'nama_ruang' => 'Ruang Serbaguna',
    'kapasitas' => 40,
    'status' => 'kosong',
    'pengguna_default' => null
]);
echo "   ✓ Ruang '{$ruangKosong->nama_ruang}' dibuat (kosong, tanpa pengguna default)\n\n";

// 2. Buat peminjaman untuk ruang dengan pengguna default
echo "2. Membuat peminjaman untuk ruang dengan pengguna default...\n";

$peminjam = User::where('role', 'peminjam')->first();
if (!$peminjam) {
    echo "   ✗ Error: Tidak ada user dengan role peminjam\n";
    exit(1);
}

$peminjaman = Peminjaman::create([
    'id_user' => $peminjam->id_user,
    'id_ruang' => $ruangDenganPengguna->id_ruang,
    'tanggal_pinjam' => now()->addDay()->format('Y-m-d'),
    'tanggal_kembali' => now()->addDay()->format('Y-m-d'),
    'waktu_mulai' => '08:00',
    'waktu_selesai' => '12:00',
    'keperluan' => 'Testing room relocation system',
    'status' => 'approved'
]);
echo "   ✓ Peminjaman dibuat (ID: {$peminjaman->id_peminjaman})\n";
echo "   ✓ Status: {$peminjaman->status}\n\n";

// 3. Test relokasi pengguna default
echo "3. Testing relokasi pengguna default...\n";

$relocationService = new RoomRelocationService();
$result = $relocationService->relocateDefaultUser($peminjaman);

if ($result['success']) {
    echo "   ✓ Relokasi berhasil!\n";
    echo "   ✓ Message: {$result['message']}\n";
    
    if ($result['relocated']) {
        echo "   ✓ Pengguna '{$result['pengguna_default']}' dipindahkan\n";
        echo "   ✓ Dari: {$result['ruang_asal']}\n";
        echo "   ✓ Ke: {$result['ruang_tujuan']}\n";
        
        // Verifikasi perubahan di database
        $ruangKosongUpdated = Ruang::find($ruangKosong->id_ruang);
        echo "\n   Verifikasi ruang tujuan:\n";
        echo "   - pengguna_default_temp: {$ruangKosongUpdated->pengguna_default_temp}\n";
        echo "   - is_temporary_occupied: " . ($ruangKosongUpdated->is_temporary_occupied ? 'true' : 'false') . "\n";
        echo "   - ruang_asal_id: {$ruangKosongUpdated->ruang_asal_id}\n";
    }
} else {
    echo "   ✗ Relokasi gagal: {$result['message']}\n";
}

echo "\n4. Testing pengembalian pengguna default...\n";

// Simulate peminjaman selesai
$peminjaman->update(['status' => 'selesai']);
echo "   ✓ Peminjaman di-set sebagai selesai\n";

$returnResult = $relocationService->returnDefaultUser($peminjaman);

if ($returnResult['success']) {
    echo "   ✓ Pengembalian berhasil!\n";
    echo "   ✓ Message: {$returnResult['message']}\n";
    
    if ($returnResult['returned']) {
        echo "   ✓ Pengguna '{$returnResult['pengguna_default']}' dikembalikan\n";
        echo "   ✓ Dari ruang sementara: {$returnResult['ruang_sementara']}\n";
        echo "   ✓ Ke ruang asal: {$returnResult['ruang_asal']}\n";
        
        // Verifikasi perubahan di database
        $ruangKosongFinal = Ruang::find($ruangKosong->id_ruang);
        echo "\n   Verifikasi ruang setelah pengembalian:\n";
        echo "   - pengguna_default_temp: " . ($ruangKosongFinal->pengguna_default_temp ?? 'null') . "\n";
        echo "   - is_temporary_occupied: " . ($ruangKosongFinal->is_temporary_occupied ? 'true' : 'false') . "\n";
        echo "   - ruang_asal_id: " . ($ruangKosongFinal->ruang_asal_id ?? 'null') . "\n";
    }
} else {
    echo "   ✗ Pengembalian gagal: {$returnResult['message']}\n";
}

// Cleanup
echo "\n5. Cleanup test data...\n";
$peminjaman->delete();
$ruangDenganPengguna->delete();
$ruangKosong->delete();
echo "   ✓ Test data dihapus\n";

echo "\n=== Testing selesai ===\n";

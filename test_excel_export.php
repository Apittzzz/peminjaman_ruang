<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Peminjaman;
use App\Models\Ruang;
use App\Models\User;

echo "=== Testing Excel Export ===\n\n";

// Check if we have peminjaman data
$count = Peminjaman::count();
echo "Total peminjaman dalam database: $count\n\n";

if ($count < 5) {
    echo "Membuat data test...\n";
    
    $user = User::where('role', 'peminjam')->first();
    $ruang = Ruang::first();
    
    if ($user && $ruang) {
        for ($i = 0; $i < 5; $i++) {
            Peminjaman::create([
                'id_user' => $user->id_user,
                'id_ruang' => $ruang->id_ruang,
                'tanggal_pinjam' => now()->subDays($i)->format('Y-m-d'),
                'tanggal_kembali' => now()->subDays($i)->format('Y-m-d'),
                'waktu_mulai' => '08:00',
                'waktu_selesai' => '12:00',
                'keperluan' => 'Test export Excel - ' . $i,
                'status' => ['pending', 'approved', 'rejected', 'selesai', 'cancelled'][$i % 5],
                'catatan' => 'Data test untuk export'
            ]);
        }
        echo "✓ 5 data test berhasil dibuat\n";
    } else {
        echo "✗ Tidak ada user atau ruang untuk membuat data test\n";
    }
}

echo "\n=== Cara Test Export ===\n";
echo "1. Login sebagai admin atau petugas\n";
echo "2. Akses: http://localhost:8000/admin/laporan\n";
echo "3. Pilih periode yang diinginkan\n";
echo "4. Klik tombol 'Excel' untuk download format Excel\n";
echo "5. Klik tombol 'CSV' untuk download format CSV\n\n";

echo "File Excel akan otomatis ter-download dengan format:\n";
echo "- Header berwarna dengan styling\n";
echo "- Status dengan color coding\n";
echo "- Border dan alignment yang rapi\n";
echo "- Auto-width columns\n\n";

echo "=== Testing Selesai ===\n";

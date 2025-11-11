<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

// Create admin user
$admin = User::where('username', 'admin')->first();
if (!$admin) {
    User::create([
        'username' => 'admin',
        'nama' => 'Administrator',
        'password' => bcrypt('admin123'),
        'role' => 'admin'
    ]);
    echo "✓ Admin user created: username=admin, password=admin123\n";
} else {
    echo "✓ Admin user already exists\n";
}

// Create peminjam user
$peminjam = User::where('username', 'peminjam')->first();
if (!$peminjam) {
    User::create([
        'username' => 'peminjam',
        'nama' => 'Test Peminjam',
        'password' => bcrypt('peminjam123'),
        'role' => 'peminjam'
    ]);
    echo "✓ Peminjam user created: username=peminjam, password=peminjam123\n";
} else {
    echo "✓ Peminjam user already exists\n";
}

// Create petugas user
$petugas = User::where('username', 'petugas')->first();
if (!$petugas) {
    User::create([
        'username' => 'petugas',
        'nama' => 'Test Petugas',
        'password' => bcrypt('petugas123'),
        'role' => 'petugas'
    ]);
    echo "✓ Petugas user created: username=petugas, password=petugas123\n";
} else {
    echo "✓ Petugas user already exists\n";
}

echo "\nTest users are ready!\n";

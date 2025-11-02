<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    { 
        // Create admin user
        User::create([
            'username' => 'admin',
            'password' => Hash::make('password'),
            'nama' => 'Administrator',
            'role' => User::ROLE_ADMIN,
        ]);

        // Create petugas user
        User::create([
            'username' => 'petugas',
            'password' => Hash::make('password'),
            'nama' => 'Petugas Ruang',
            'role' => User::ROLE_PETUGAS,
        ]);

        // Create sample peminjam
        User::create([
            'username' => 'peminjam',
            'password' => Hash::make('password'),
            'nama' => 'John Doe',
            'role' => User::ROLE_PEMINJAM,
        ]);

        // Create sample ruangan
        \App\Models\Ruang::create([
            'nama_ruang' => 'Ruang Aula',
            'kapasitas' => 100,
            'status' => 'kosong',
        ]);

        \App\Models\Ruang::create([
            'nama_ruang' => 'Ruang Kelas 101',
            'kapasitas' => 40,
            'status' => 'kosong',
        ]);

        \App\Models\Ruang::create([
            'nama_ruang' => 'Laboratorium Komputer',
            'kapasitas' => 30,
            'status' => 'kosong',
        ]);
    }
}
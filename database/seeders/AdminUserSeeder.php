<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'nama' => 'Administrator',
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'petugas1',
                'password' => Hash::make('petugas123'),
                'nama' => 'Petugas Sekolah',
                'role' => 'petugas',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
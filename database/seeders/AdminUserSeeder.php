<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Check apakah admin sudah ada
        $adminExists = DB::table('users')->where('username', 'admin')->exists();
        $petugasExists = DB::table('users')->where('username', 'petugas1')->exists();

        $users = [];

        if (!$adminExists) {
            $users[] = [
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'nama' => 'Administrator',
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!$petugasExists) {
            $users[] = [
                'username' => 'petugas1',
                'password' => Hash::make('petugas123'),
                'nama' => 'Petugas Sekolah',
                'role' => 'petugas',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($users)) {
            DB::table('users')->insert($users);
            $this->command->info('✅ User seeded successfully!');
        } else {
            $this->command->info('ℹ️  Users already exist, skipping...');
        }
    }
}
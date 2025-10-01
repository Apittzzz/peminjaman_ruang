# Sistem Peminjaman Ruang

Sistem untuk mengelola peminjaman ruangan berbasis web dengan Laravel 12.

## Fitur

- ✅ Multi-role Authentication (Admin, Petugas, Peminjam)
- ✅ Manajemen User
- ✅ Manajemen Ruang
- ❌ Pengajuan Peminjaman (Dalam Pengembangan)
- ❌ Persetujuan Peminjaman (Dalam Pengembangan)
- ❌ Generate Laporan (Dalam Pengembangan)

## Role dan Hak Akses

### Admin
- Manajemen User
- Manajemen Ruang
- Generate Laporan

### Petugas
- Persetujuan Peminjaman
- Generate Laporan

### Peminjam
- Pengajuan Peminjaman
- Melihat Jadwal Ruang

## Instalasi

1. Clone repository
2. `composer install`
3. `cp .env.example .env`
4. `php artisan key:generate`
5. Setup database di .env
6. `php artisan migrate`
7. `php artisan db:seed`
8. `php artisan serve`

## Default Accounts

- **Admin**: admin / password
- **Petugas**: petugas / password
- **Peminjam**: peminjam / password

## Teknologi

- Laravel 12
- Bootstrap 5
- MySQL
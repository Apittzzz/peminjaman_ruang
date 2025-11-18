# 🏢 Sistem Peminjaman Ruang

> Sistem manajemen peminjaman ruangan berbasis web menggunakan Laravel 11

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

---

## 📋 Daftar Isi

- [Fitur](#-fitur)
- [Role & Hak Akses](#-role--hak-akses)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Default Accounts](#-default-accounts)
- [Teknologi](#-teknologi)
- [Dokumentasi](#-dokumentasi)
- [Screenshot](#-screenshot)
- [Contributing](#-contributing)
- [License](#-license)

---

## ✨ Fitur

### Sistem Autentikasi
- ✅ Multi-role authentication (Admin, Petugas, Peminjam)
- ✅ Login & Register
- ✅ Session management dengan database driver
- ✅ Role-based access control

### Manajemen User
- ✅ CRUD users (Admin only)
- ✅ Role management (Admin, Petugas, Peminjam)
- ✅ User profile management

### Manajemen Ruangan
- ✅ CRUD ruangan
- ✅ Status ruangan (Kosong/Dipakai)
- ✅ Pengguna default ruangan
- ✅ Automatic room relocation system
- ✅ Temporary occupancy tracking

### Peminjaman Ruangan
- ✅ Form pengajuan peminjaman
- ✅ Multi-day booking support
- ✅ Flexible time validation
- ✅ Status tracking (Pending, Approved, Rejected, Selesai, Cancelled)
- ✅ Booking history untuk peminjam

### Persetujuan Peminjaman
- ✅ Review peminjaman (Admin & Petugas)
- ✅ Approve/Reject dengan catatan
- ✅ Automatic room status update
- ✅ Default user relocation on approval

### Jadwal & Monitoring
- ✅ View jadwal ruangan
- ✅ Filter by date & room status
- ✅ Real-time occupancy display
- ✅ Temporary occupant indicators

### Laporan
- ✅ Generate laporan by periode (Hari ini, Minggu ini, Bulan ini, Tahun ini)
- ✅ Statistik peminjaman
- ✅ Top 5 ruangan populer
- ✅ Top 5 peminjam terbanyak
- ✅ Export to CSV
- ✅ Export to Excel (PhpSpreadsheet)

### Automation
- ✅ Automatic booking completion (Cron job)
- ✅ Automatic room status update
- ✅ Automatic default user return after booking ends

---

## 👥 Role & Hak Akses

### 🔐 Admin
**Full Access - Manage Everything**

- ✅ Manajemen User (Create, Read, Update, Delete)
- ✅ Manajemen Ruang (Create, Read, Update, Delete)
- ✅ Kelola Peminjaman (View All, Approve, Reject)
- ✅ Generate Laporan & Export
- ✅ View Jadwal Ruangan

### 👔 Petugas
**Operational Management**

- ✅ Persetujuan Peminjaman (Approve, Reject)
- ✅ Generate Laporan & Export
- ✅ View Jadwal Ruangan
- ✅ View All Bookings

### 👤 Peminjam
**User Access**

- ✅ Ajukan Peminjaman Ruangan
- ✅ View My Bookings
- ✅ Cancel Pending Bookings
- ✅ View Jadwal Ruangan (Availability)

---

## 🚀 Instalasi

### Prerequisites

Pastikan system Anda sudah terinstall:

- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Node.js >= 18.x
- NPM atau Yarn

### Step-by-Step Installation

```bash
# 1. Clone repository
git clone https://github.com/Apittzzz/peminjaman_ruang.git
cd peminjaman_ruang

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Configure database di .env
# Edit file .env, sesuaikan dengan database Anda:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=peminjaman_ruang
DB_USERNAME=root
DB_PASSWORD=

# 7. Create database
mysql -u root -p
CREATE DATABASE peminjaman_ruang;
exit;

# 8. Run migrations
php artisan migrate

# 9. Seed database (create admin user & sample data)
php artisan db:seed --class=AdminUserSeeder

# 10. Create sessions table
php artisan session:table
php artisan migrate

# 11. Build frontend assets
npm run dev
# atau untuk production:
npm run build

# 12. Create storage link
php artisan storage:link

# 13. Start development server
php artisan serve

# Server akan berjalan di: http://localhost:8000
```

---

## ⚙️ Konfigurasi

### Session Configuration

Update `.env`:

```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

### Cron Job Setup (Production)

Untuk automatic booking completion, tambahkan ke crontab:

```bash
# Edit crontab
crontab -e

# Tambahkan line ini:
* * * * * cd /path/to/peminjaman_ruang && php artisan schedule:run >> /dev/null 2>&1
```

Atau manual run command:

```bash
php artisan bookings:mark-finished
```

### Email Configuration (Optional)

Jika ingin enable email notifications, update `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 🔑 Default Accounts

Setelah seeding database, gunakan akun berikut untuk login:

### Admin
```
Username: admin
Password: admin123
```

### Petugas (Optional - create manually)
```
Username: petugas
Password: petugas123
```

### Peminjam (Optional - register via form)
```
Username: peminjam
Password: peminjam123
```

**⚠️ PENTING:** Segera ubah password default setelah login pertama kali!

---

## 🛠️ Teknologi

### Backend
- **Laravel 11.x** - PHP Framework
- **MySQL 8.0** - Database
- **PHP 8.2+** - Programming Language

### Frontend
- **Bootstrap 5.3** - CSS Framework
- **Font Awesome 6.4** - Icon Library
- **Vite** - Frontend Build Tool
- **Blade** - Templating Engine

### Libraries
- **Laravel Sanctum** - API Authentication
- **PhpSpreadsheet** - Excel Export
- **Carbon** - Date/Time Manipulation
- **Laravel Debugbar** - Development Tool (dev only)

---

## 📚 Dokumentasi

- 📁 **[STRUKTUR_PROJECT.md](STRUKTUR_PROJECT.md)** - Detailed project structure
- 🛠️ **[DEVELOPMENT_GUIDE.md](DEVELOPMENT_GUIDE.md)** - Developer guidelines
- 🧪 **[scripts/README.md](scripts/README.md)** - Testing scripts documentation

---

## 📸 Screenshot

### Login Page
![Login](docs/screenshots/login.png)

### Admin Dashboard
![Admin Dashboard](docs/screenshots/admin-dashboard.png)

### Booking Form
![Booking Form](docs/screenshots/booking-form.png)

### Report
![Report](docs/screenshots/report.png)

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'feat: Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

Lihat [DEVELOPMENT_GUIDE.md](DEVELOPMENT_GUIDE.md) untuk code standards dan conventions.

---

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Author

**Apittzzz**

- GitHub: [@Apittzzz](https://github.com/Apittzzz)
- Repository: [peminjaman_ruang](https://github.com/Apittzzz/peminjaman_ruang)

---

## 🙏 Acknowledgments

- Laravel Community
- Bootstrap Team
- Font Awesome Team
- All Contributors

---

## 📞 Support

Jika menemukan bug atau ingin request fitur baru:

- 🐛 [Report Bug](https://github.com/Apittzzz/peminjaman_ruang/issues)
- 💡 [Request Feature](https://github.com/Apittzzz/peminjaman_ruang/issues)

---

**⭐ Jangan lupa star repository ini jika bermanfaat!**

*Made with ❤️ using Laravel*
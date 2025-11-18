# 📁 STRUKTUR PROJECT - SISTEM PEMINJAMAN RUANG

> **Framework:** Laravel 11.x  
> **Last Updated:** 2025-11-13  
> **Status:** Production Ready

---

## 🏗️ STRUKTUR FOLDER UTAMA

```
peminjaman_ruang/
├── app/                          # Core Application
│   ├── Console/
│   │   └── Commands/            # Artisan Commands
│   │       └── MarkFinishedBookings.php
│   ├── Http/
│   │   ├── Controllers/         # Business Logic Controllers
│   │   │   ├── Admin/          # Admin Controllers
│   │   │   │   ├── LaporanController.php
│   │   │   │   ├── PeminjamanController.php
│   │   │   │   ├── RuangController.php
│   │   │   │   └── UserController.php
│   │   │   ├── Api/            # API Controllers
│   │   │   │   └── PeminjamanController.php
│   │   │   ├── Peminjam/       # Peminjam Controllers
│   │   │   │   └── PeminjamanController.php
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── JadwalController.php
│   │   │   └── PersetujuanUmumController.php
│   │   ├── Middleware/         # Custom Middleware
│   │   │   ├── AdminMiddleware.php
│   │   │   ├── PetugasMiddleware.php
│   │   │   └── PeminjamMiddleware.php
│   │   └── Requests/           # Form Request Validation
│   ├── Models/                 # Eloquent Models (Database)
│   │   ├── User.php           # User Model (Admin, Petugas, Peminjam)
│   │   ├── Ruang.php          # Room Model
│   │   ├── Peminjaman.php     # Booking Model
│   │   └── Laporan.php        # Report Model
│   ├── Services/              # Business Logic Services
│   │   └── RoomRelocationService.php
│   └── Providers/
│       └── AppServiceProvider.php
│
├── config/                     # Configuration Files
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   └── ...
│
├── database/
│   ├── migrations/            # Database Schema
│   │   ├── *_create_users_table.php
│   │   ├── *_create_ruang_table.php
│   │   ├── *_create_peminjaman_table.php
│   │   ├── *_create_laporan_table.php
│   │   ├── *_add_pengguna_default_to_ruang_table.php
│   │   └── *_add_temporary_relocation_to_ruang_table.php
│   ├── seeders/               # Database Seeders
│   │   ├── AdminUserSeeder.php
│   │   └── UserSeeder.php
│   └── factories/
│       └── UserFactory.php
│
├── public/                     # Public Assets
│   ├── css/
│   │   └── style.css         # Main Stylesheet (Centralized)
│   ├── build/                # Compiled Assets (Vite)
│   └── index.php             # Entry Point
│
├── resources/
│   ├── views/                # Blade Templates
│   │   ├── layouts/
│   │   │   └── app.blade.php        # Main Layout
│   │   ├── components/              # Reusable Components
│   │   ├── auth/                    # Authentication Views
│   │   │   ├── login.blade.php
│   │   │   └── register.blade.php
│   │   ├── dashboard/               # Dashboard Views
│   │   │   ├── admin.blade.php
│   │   │   ├── petugas.blade.php
│   │   │   └── peminjam.blade.php
│   │   ├── admin/                   # Admin Feature Views
│   │   │   ├── users/
│   │   │   ├── ruang/
│   │   │   ├── peminjaman/
│   │   │   ├── persetujuan/
│   │   │   └── laporan/
│   │   ├── peminjam/                # Peminjam Feature Views
│   │   │   └── peminjaman/
│   │   └── jadwal/                  # Schedule Views
│   │       └── index.blade.php
│   ├── css/
│   │   └── app.css
│   └── js/
│       └── app.js
│
├── routes/
│   ├── web.php               # Web Routes (Main)
│   ├── api.php               # API Routes
│   ├── auth.php              # Auth Routes
│   └── console.php           # Console Routes
│
├── storage/                   # Storage (Logs, Cache, Sessions)
│   ├── app/
│   ├── framework/
│   │   ├── cache/
│   │   ├── sessions/
│   │   └── views/
│   └── logs/
│
├── tests/                     # Testing
│   ├── Feature/
│   └── Unit/
│
└── scripts/                   # Development Scripts (akan dibuat)
    ├── test_scripts/         # Test Scripts
    └── dev_tools/            # Development Tools
```

---

## 🎯 PENJELASAN SETIAP KOMPONEN

### **1. Controllers (app/Http/Controllers/)**

**Fungsi:** Handle request dari user, proses logic, return response

| Controller | Fungsi | Role Access |
|------------|--------|-------------|
| `AuthController` | Login, Logout, Register | Public |
| `DashboardController` | Dashboard untuk semua role | All authenticated |
| `Admin/UserController` | CRUD users | Admin only |
| `Admin/RuangController` | CRUD ruangan | Admin only |
| `Admin/PeminjamanController` | Kelola peminjaman | Admin only |
| `Admin/LaporanController` | Generate laporan & export | Admin, Petugas |
| `Peminjam/PeminjamanController` | Ajukan peminjaman | Peminjam only |
| `PersetujuanUmumController` | Approve/reject peminjaman | Admin, Petugas |
| `JadwalController` | Lihat jadwal ruangan | All authenticated |
| `Api/PeminjamanController` | API endpoint | API token |

### **2. Models (app/Models/)**

**Fungsi:** Representasi tabel database, handle query

| Model | Table | Relationships |
|-------|-------|---------------|
| `User` | users | hasMany(Peminjaman) |
| `Ruang` | ruang | hasMany(Peminjaman), belongsTo(User as default) |
| `Peminjaman` | peminjaman | belongsTo(User), belongsTo(Ruang) |
| `Laporan` | laporan | - |

### **3. Services (app/Services/)**

**Fungsi:** Business logic yang complex dan reusable

| Service | Fungsi |
|---------|--------|
| `RoomRelocationService` | Handle automatic relocation of default room users |

### **4. Middleware (app/Http/Middleware/)**

**Fungsi:** Filter HTTP request sebelum masuk controller

| Middleware | Fungsi |
|------------|--------|
| `AdminMiddleware` | Check if user role = admin |
| `PetugasMiddleware` | Check if user role = petugas |
| `PeminjamMiddleware` | Check if user role = peminjam |

### **5. Migrations (database/migrations/)**

**Fungsi:** Version control untuk database schema

**Urutan Eksekusi:**
1. `create_users_table` - Tabel users
2. `create_ruang_table` - Tabel ruangan
3. `create_peminjaman_table` - Tabel peminjaman
4. `create_laporan_table` - Tabel laporan
5. `add_pengguna_default_to_ruang_table` - Tambah kolom pengguna_default
6. `add_temporary_relocation_to_ruang_table` - Tambah fitur relokasi temporary

### **6. Views (resources/views/)**

**Fungsi:** Template HTML menggunakan Blade engine

**Layout Hierarchy:**
```
layouts/app.blade.php (Master)
├── auth/login.blade.php
├── auth/register.blade.php
├── dashboard/admin.blade.php
├── dashboard/petugas.blade.php
├── dashboard/peminjam.blade.php
└── [feature]/[action].blade.php
```

---

## 🔄 FLOW APLIKASI

### **1. Authentication Flow**
```
User Access → web.php routes
├── Login: GET /login → AuthController@showLogin
├── Login Submit: POST /login → AuthController@login
├── Register: GET /register → AuthController@showRegister
├── Register Submit: POST /register → AuthController@register
└── Logout: POST /logout → AuthController@logout
```

### **2. Booking Flow (Peminjam)**
```
1. Peminjam Login
2. Dashboard Peminjam → View available rooms
3. Create Booking: /peminjam/peminjaman/create
4. Submit → PeminjamanController@store
5. Validation → Save to DB (status: pending)
6. Redirect to /peminjam/peminjaman (view my bookings)
```

### **3. Approval Flow (Admin/Petugas)**
```
1. Admin/Petugas Login
2. View pending bookings: /persetujuan
3. Review booking details
4. Approve/Reject → PersetujuanUmumController@approve/reject
5. If Approve + Room has default user:
   ├── RoomRelocationService@relocateDefaultUser()
   ├── Find available room
   ├── Move default user temporarily
   └── Update booking status
6. Notification sent to peminjam
```

### **4. Room Status Update (Automated)**
```
Cron Job (every minute):
└── Command: MarkFinishedBookings
    ├── Check peminjaman with status='approved'
    ├── Check if tanggal_kembali + jam_selesai < now()
    ├── Update status to 'selesai'
    ├── RoomRelocationService@returnDefaultUser()
    └── Update room status to 'kosong'
```

### **5. Reporting Flow (Admin/Petugas)**
```
1. Access: /admin/laporan or /petugas/laporan
2. Filter by periode (hari_ini, minggu_ini, bulan_ini, tahun_ini)
3. View statistics & charts
4. Export: /admin/laporan/export?periode=X&format=excel/csv
5. Generate file with PhpSpreadsheet
6. Download report
```

---

## 🛣️ ROUTING STRUCTURE

### **Public Routes (Tidak perlu login)**
```php
GET  /           → Welcome page
GET  /login      → Login form
POST /login      → Process login
GET  /register   → Register form
POST /register   → Process registration
```

### **Authenticated Routes (Semua role)**
```php
GET  /dashboard              → Dashboard by role
GET  /jadwal                 → View room schedule
POST /logout                 → Logout
```

### **Admin Routes**
```php
Prefix: /admin
├── /users                   → CRUD users
├── /ruang                   → CRUD rooms
├── /peminjaman              → View all bookings
└── /laporan                 → Reports & export
```

### **Petugas Routes**
```php
Prefix: /petugas
├── /persetujuan             → Approve/reject bookings
└── /laporan                 → View reports
```

### **Peminjam Routes**
```php
Prefix: /peminjam
└── /peminjaman              → My bookings (create, view, cancel)
```

### **API Routes**
```php
Prefix: /api
└── /peminjaman              → Booking API (with token)
```

---

## 🎨 STYLING ARCHITECTURE

**Framework:** Bootstrap 5.3.0  
**Icons:** Font Awesome 6.4.0  
**Custom CSS:** `/public/css/style.css` (Centralized)

**CSS Organization:**
```css
/* Root Variables */
:root { --navy, --alt, --primary, --secondary }

/* Sections */
1. Dashboard Styles (.action-card)
2. Jadwal Styles (.jadwal-card, accordion)
3. Peminjaman Styles (forms, buttons)
4. Laporan Styles (.laporan-card, .stat-card)
5. Persetujuan Styles
6. Auth Styles (.login-card, .register-card)
7. Utilities (colors, hover effects)
```

---

## 📊 DATABASE SCHEMA

### **Users Table**
```
id, username, nama, email, password, role (admin/petugas/peminjam), created_at, updated_at
```

### **Ruang Table**
```
id, nama_ruang, kapasitas, lokasi, fasilitas, status (kosong/dipakai), 
pengguna_default (FK to users), pengguna_default_temp, ruang_asal_id, 
is_temporary_occupied, created_at, updated_at
```

### **Peminjaman Table**
```
id, id_user (FK), id_ruang (FK), tanggal_pinjam, tanggal_kembali, 
jam_mulai, jam_selesai, keperluan, status (pending/approved/rejected/selesai/cancelled),
catatan, created_at, updated_at
```

---

## 🔐 SECURITY FEATURES

1. **CSRF Protection** - All POST forms protected
2. **Password Hashing** - bcrypt/Argon2
3. **SQL Injection Prevention** - Eloquent ORM
4. **XSS Prevention** - Blade auto-escaping
5. **Role-based Access Control** - Middleware
6. **Session Management** - Database driver
7. **API Token Authentication** - Sanctum

---

## 🚀 DEPLOYMENT CHECKLIST

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Generate production key: `php artisan key:generate`
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Seed admin user: `php artisan db:seed --class=AdminUserSeeder`
- [ ] Clear caches: `php artisan optimize:clear`
- [ ] Build assets: `npm run build`
- [ ] Setup cron job for commands
- [ ] Configure web server (Nginx/Apache)
- [ ] Setup SSL certificate
- [ ] Backup database regularly

---

## 📝 MAINTENANCE TASKS

### Daily
- Monitor logs: `storage/logs/laravel.log`
- Check failed jobs

### Weekly
- Database backup
- Clear old sessions: `php artisan session:clear`

### Monthly
- Update dependencies: `composer update`
- Security audit: `composer audit`
- Clean storage: `php artisan storage:link`

---

## 📚 REFERENSI

- **Laravel Docs:** https://laravel.com/docs/11.x
- **Bootstrap Docs:** https://getbootstrap.com/docs/5.3
- **Font Awesome:** https://fontawesome.com/icons

---

**Maintained by:** Apittzzz  
**Repository:** https://github.com/Apittzzz/peminjaman_ruang

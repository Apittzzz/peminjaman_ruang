# 💻 Perintah Eksekusi Program

## Daftar Isi
1. [Eksekusi Berbasis Teks (CLI)](#1-eksekusi-berbasis-teks-cli)
2. [Eksekusi Berbasis Grafik (Web)](#2-eksekusi-berbasis-grafik-web)
3. [Eksekusi Multimedia](#3-eksekusi-multimedia)
4. [Scheduled Tasks](#4-scheduled-tasks)

---

## 1. Eksekusi Berbasis Teks (CLI)

### 1.1 Artisan Commands

#### Command: Mark Finished Peminjaman

**File:** `app/Console/Commands/MarkFinishedPeminjaman.php`

**Purpose:** Auto-update status peminjaman yang sudah lewat waktu menjadi 'selesai'

**Execution:**
```bash
# Manual execution
php artisan peminjaman:mark-finished

# Scheduled execution (via cron)
* * * * * cd /path/to/project && php artisan schedule:run
```

**Implementation:**
```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Peminjaman;
use Carbon\Carbon;

class MarkFinishedPeminjaman extends Command
{
    protected $signature = 'peminjaman:mark-finished';
    protected $description = 'Mark peminjaman as finished if end date/time has passed';

    public function handle()
    {
        $now = Carbon::now();
        
        $finished = Peminjaman::where('status', 'approved')
            ->where(function ($query) use ($now) {
                $query->where('tanggal_kembali', '<', $now->format('Y-m-d'))
                    ->orWhere(function ($q) use ($now) {
                        $q->where('tanggal_kembali', '=', $now->format('Y-m-d'))
                          ->where('waktu_selesai', '<', $now->format('H:i'));
                    });
            })
            ->get();

        foreach ($finished as $peminjaman) {
            $peminjaman->update(['status' => 'selesai']);
            $this->info("Peminjaman #{$peminjaman->id_peminjaman} marked as finished");
        }

        $this->info("Total: {$finished->count()} peminjaman marked as finished");
        
        return Command::SUCCESS;
    }
}
```

**Output Example:**
```
Peminjaman #13 marked as finished
Peminjaman #24 marked as finished
Total: 2 peminjaman marked as finished
```

---

#### Command: Refresh Room Status

**File:** `app/Console/Commands/RefreshRuangStatus.php`

**Purpose:** Sync room status based on active bookings

**Execution:**
```bash
php artisan ruang:refresh-status
```

**Implementation:**
```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ruang;
use App\Models\Peminjaman;
use Carbon\Carbon;

class RefreshRuangStatus extends Command
{
    protected $signature = 'ruang:refresh-status';
    protected $description = 'Refresh room status based on active bookings';

    public function handle()
    {
        $now = Carbon::now();
        $updated = 0;

        $ruangs = Ruang::all();

        foreach ($ruangs as $ruang) {
            $hasActiveBooking = Peminjaman::where('id_ruang', $ruang->id_ruang)
                ->where('status', 'approved')
                ->where('tanggal_pinjam', '<=', $now->format('Y-m-d'))
                ->where('tanggal_kembali', '>=', $now->format('Y-m-d'))
                ->exists();

            $newStatus = $hasActiveBooking ? 'dipakai' : 'kosong';

            if ($ruang->status !== $newStatus) {
                $ruang->update(['status' => $newStatus]);
                $this->info("Ruang '{$ruang->nama_ruang}' updated to: {$newStatus}");
                $updated++;
            }
        }

        $this->info("Total: {$updated} ruangan updated");
        
        return Command::SUCCESS;
    }
}
```

---

#### Database Commands

```bash
# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Fresh migration (drop all tables and re-migrate)
php artisan migrate:fresh

# Seed database
php artisan db:seed

# Seed specific seeder
php artisan db:seed --class=AdminUserSeeder

# Fresh migrate + seed
php artisan migrate:fresh --seed
```

---

#### Cache Commands

```bash
# Clear all caches
php artisan optimize:clear

# Clear specific caches
php artisan config:clear    # Config cache
php artisan route:clear     # Route cache
php artisan view:clear      # Compiled views
php artisan cache:clear     # Application cache

# Create caches (production)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

#### Development Server

```bash
# Start development server
php artisan serve

# With custom host and port
php artisan serve --host=0.0.0.0 --port=8080
```

---

### 1.2 Tinker (Interactive Shell)

**Tinker** adalah REPL (Read-Eval-Print Loop) untuk testing code interactively.

**Start Tinker:**
```bash
php artisan tinker
```

**Example Usage:**

```php
// Create user
>>> $user = new App\Models\User;
>>> $user->username = 'test123';
>>> $user->password = Hash::make('password');
>>> $user->nama = 'Test User';
>>> $user->role = 'peminjam';
>>> $user->save();
=> true

// Query data
>>> App\Models\Peminjaman::where('status', 'pending')->count();
=> 5

// Test relationships
>>> $peminjaman = App\Models\Peminjaman::first();
>>> $peminjaman->user->nama;
=> "John Doe"

// Update data
>>> $ruang = App\Models\Ruang::find(1);
>>> $ruang->update(['status' => 'kosong']);
=> true

// Delete data
>>> App\Models\Peminjaman::where('status', 'cancelled')->delete();
=> 3
```

---

### 1.3 Database Commands via Artisan

```bash
# Database info
php artisan db:show

# Table info
php artisan db:table users

# Monitor database queries
php artisan db:monitor --databases=mysql --max=100
```

---

## 2. Eksekusi Berbasis Grafik (Web)

### 2.1 Interactive Forms

#### Login Form
**File:** `resources/views/auth/login.blade.php`

**Features:**
- Input validation dengan visual feedback
- Show/hide password toggle
- CSRF protection
- Responsive layout

**User Interaction Flow:**
```
1. User buka /login
2. Input username & password
3. Click toggle untuk show/hide password
4. Submit form
5. Frontend validation (required fields)
6. Server-side validation
7. Success → Redirect ke dashboard
   Failed → Show error message di form
```

**Visual Elements:**
- Input fields dengan focus states
- Error messages dalam red alert
- Success feedback dengan green notification
- Loading spinner saat submit

---

#### Peminjaman Form
**File:** `resources/views/peminjam/peminjaman/create.blade.php`

**Interactive Features:**
1. **Dynamic Room Status**
   ```javascript
   // Update status badge saat pilih ruangan
   ruangSelect.addEventListener('change', function() {
       const selectedOption = this.options[this.selectedIndex];
       if (selectedOption.value) {
           ruangStatus.innerHTML = `<span class="badge bg-success">Tersedia</span>`;
       }
   });
   ```

2. **Date Validation**
   ```javascript
   // Tanggal kembali min = tanggal pinjam
   tanggalPinjam.addEventListener('change', function() {
       tanggalKembali.min = this.value;
   });
   ```

3. **Time Range Validation**
   ```javascript
   // Validate waktu selesai > waktu mulai (same date)
   function validateTimeRange() {
       if (tanggalPinjam.value === tanggalKembali.value) {
           if (waktuSelesai.value <= waktuMulai.value) {
               waktuSelesai.classList.add('is-invalid');
               return false;
           }
       }
       return true;
   }
   ```

**User Interaction Flow:**
```
1. Select ruangan → Status badge updates
2. Select tanggal_pinjam → tanggal_kembali min updates
3. Input waktu → Real-time validation
4. Submit → Frontend validation
5. Ajax check availability (optional)
6. Server validation
7. Success → Redirect dengan notification
   Failed → Show errors inline
```

---

### 2.2 Data Tables

#### Responsive Table
**File:** `resources/views/peminjam/peminjaman/index.blade.php`

**Features:**
- Sortable columns
- Responsive hiding (d-none d-md-table-cell)
- Status badges dengan warna
- Action buttons (view, edit, delete)
- Pagination

**Interaction:**
```html
<table class="table table-hover">
    <thead>
        <tr>
            <th>Ruangan</th>
            <th class="d-none d-md-table-cell">Tanggal</th>
            <th class="d-none d-lg-table-cell">Keperluan</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($peminjaman as $item)
        <tr>
            <td>
                {{ $item->ruang->nama_ruang }}
                <span class="d-block d-md-none">
                    <small>{{ $item->tanggal_pinjam }}</small>
                </span>
            </td>
            <td class="d-none d-md-table-cell">
                {{ $item->tanggal_pinjam }}
            </td>
            <td>
                <span class="badge bg-success">{{ $item->status }}</span>
            </td>
            <td>
                <button class="btn btn-sm btn-primary">Detail</button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
```

---

### 2.3 Modal Dialogs

#### Edit Pengguna Modal
**File:** `resources/views/jadwal/index.blade.php`

**Trigger:**
```html
<button data-bs-toggle="modal" data-bs-target="#editPenggunaModal{{ $ruang->id_ruang }}">
    Edit Pengguna
</button>
```

**Modal Structure:**
```html
<div class="modal fade" id="editPenggunaModal{{ $ruang->id_ruang }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Edit Pengguna Default</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <!-- Form fields -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
```

**JavaScript Interaction:**
```javascript
// Dynamic show/hide fields based on status
statusSelect.addEventListener('change', function() {
    if (this.value === 'dipakai') {
        penggunaDefaultFields.style.display = 'block';
        penggunaDefaultInput.required = true;
    } else {
        penggunaDefaultFields.style.display = 'none';
        penggunaDefaultInput.required = false;
    }
});
```

---

### 2.4 Accordion Components

#### Jadwal Ruangan Accordion
**File:** `resources/views/jadwal/index.blade.php`

**Structure:**
```html
<div class="accordion" id="jadwalRuangan">
    @foreach($ruangs as $ruang)
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#collapse{{ $ruang->id_ruang }}">
                {{ $ruang->nama_ruang }}
                <span class="badge bg-success">Kosong</span>
            </button>
        </h2>
        <div id="collapse{{ $ruang->id_ruang }}" 
             class="accordion-collapse collapse">
            <div class="accordion-body">
                <!-- Room details & bookings -->
            </div>
        </div>
    </div>
    @endforeach
</div>
```

**User Interaction:**
1. Click accordion header → Expand/collapse
2. View room details & active bookings
3. Click "Ajukan Peminjaman" button (for peminjam)
4. Smooth animation via Bootstrap

---

### 2.5 Dashboard Statistics

#### Stats Cards
**File:** `resources/views/dashboard/admin.blade.php`

**Visual Components:**
```html
<div class="row">
    <div class="col-6 col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <i class="fas fa-users fa-2x text-primary"></i>
                <h3>{{ $totalUsers }}</h3>
                <p class="text-muted">Total Users</p>
            </div>
        </div>
    </div>
    <!-- More stat cards -->
</div>
```

**CSS Styling:**
```css
.stat-card {
    border-left: 4px solid #007bff;
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
```

---

## 3. Eksekusi Multimedia

### 3.1 Icons & Typography

#### Font Awesome Icons

**Usage in Blade:**
```blade
<!-- Menu icons -->
<i class="fas fa-tachometer-alt"></i> Dashboard
<i class="fas fa-users"></i> Manajemen User
<i class="fas fa-door-open"></i> Ruangan
<i class="fas fa-calendar-alt"></i> Jadwal

<!-- Status icons -->
<i class="fas fa-check-circle text-success"></i> Approved
<i class="fas fa-clock text-warning"></i> Pending
<i class="fas fa-times-circle text-danger"></i> Rejected

<!-- Action icons -->
<i class="fas fa-eye"></i> Detail
<i class="fas fa-edit"></i> Edit
<i class="fas fa-trash"></i> Delete
<i class="fas fa-plus"></i> Tambah
```

**Icon Libraries:**
- Font Awesome 6.0 (CDN)
- Bootstrap Icons (optional)

---

### 3.2 Status Badges dengan Warna

**Implementation:**
```blade
@php
$statusColors = [
    'pending' => 'warning',
    'approved' => 'success',
    'rejected' => 'danger',
    'cancelled' => 'secondary',
    'selesai' => 'info'
];
@endphp

<span class="badge bg-{{ $statusColors[$peminjaman->status] }}">
    {{ ucfirst($peminjaman->status) }}
</span>
```

**Visual Output:**
- 🟡 Pending (Yellow)
- 🟢 Approved (Green)
- 🔴 Rejected (Red)
- ⚫ Cancelled (Gray)
- 🔵 Selesai (Blue)

---

### 3.3 Alert Boxes

**Types:**
```blade
<!-- Success -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Error -->
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-triangle me-2"></i>
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Warning -->
<div class="alert alert-warning">
    <i class="fas fa-info-circle me-2"></i>
    Ruangan ini memiliki pengguna default.
</div>

<!-- Info -->
<div class="alert alert-info">
    <i class="fas fa-lightbulb me-2"></i>
    Peminjaman akan diproses dalam 1x24 jam.
</div>
```

---

### 3.4 Hover Effects & Animations

**CSS:**
```css
/* Button hover */
.btn-primary {
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0,123,255,0.3);
}

/* Card hover */
.action-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.action-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

/* Table row hover */
.table-hover tbody tr:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}
```

---

### 3.5 Loading Indicators

**Spinner (Bootstrap):**
```html
<button type="submit" class="btn btn-primary" id="submitBtn">
    <span class="spinner-border spinner-border-sm d-none" id="spinner"></span>
    <span id="btnText">Submit</span>
</button>

<script>
document.getElementById('submitBtn').addEventListener('click', function() {
    document.getElementById('spinner').classList.remove('d-none');
    document.getElementById('btnText').textContent = 'Processing...';
});
</script>
```

**Progress Bar:**
```html
<div class="progress">
    <div class="progress-bar progress-bar-striped progress-bar-animated" 
         role="progressbar" 
         style="width: 75%">
        75%
    </div>
</div>
```

---

## 4. Scheduled Tasks

### 4.1 Cron Job Setup

**File:** `app/Console/Kernel.php`

```php
<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Mark finished peminjaman every 5 minutes
        $schedule->command('peminjaman:mark-finished')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->onOneServer();

        // Refresh room status every 10 minutes
        $schedule->command('ruang:refresh-status')
            ->everyTenMinutes()
            ->withoutOverlapping();

        // Daily cleanup of old notifications (if implemented)
        $schedule->command('notifications:cleanup')
            ->daily()
            ->at('02:00');

        // Weekly database backup (if implemented)
        $schedule->command('backup:run')
            ->weekly()
            ->sundays()
            ->at('03:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
```

---

### 4.2 Cron Entry (Production)

**Linux Cron:**
```bash
# Edit crontab
crontab -e

# Add this line
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

**cPanel Cron Jobs:**
```
Common Settings: Every Minute (* * * * *)
Command: /usr/local/bin/php /home/username/public_html/artisan schedule:run
```

---

### 4.3 Test Scheduled Tasks

**Local Testing:**
```bash
# Run scheduler manually
php artisan schedule:run

# Test specific command
php artisan peminjaman:mark-finished

# See scheduled tasks list
php artisan schedule:list
```

**Output:**
```
  0 */5 * * *  php artisan peminjaman:mark-finished .... Next Due: 5 minutes
  0 */10 * * * php artisan ruang:refresh-status ....... Next Due: 10 minutes
  0 2 * * *    php artisan notifications:cleanup ..... Next Due: 1 day
```

---

## 🎯 Latihan Soal

### Soal 1: Command Creation
**Q:** Buatlah artisan command untuk export laporan peminjaman bulanan ke CSV.

<details>
<summary>Jawaban</summary>

```bash
# Generate command
php artisan make:command ExportMonthlyReport
```

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Peminjaman;
use Carbon\Carbon;

class ExportMonthlyReport extends Command
{
    protected $signature = 'report:export-monthly {month?} {year?}';
    protected $description = 'Export monthly peminjaman report to CSV';

    public function handle()
    {
        $month = $this->argument('month') ?? Carbon::now()->month;
        $year = $this->argument('year') ?? Carbon::now()->year;

        $peminjaman = Peminjaman::with(['user', 'ruang'])
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();

        $filename = "report_{$year}_{$month}.csv";
        $file = fopen(storage_path("app/{$filename}"), 'w');

        fputcsv($file, ['ID', 'User', 'Ruang', 'Tanggal', 'Status']);

        foreach ($peminjaman as $p) {
            fputcsv($file, [
                $p->id_peminjaman,
                $p->user->nama,
                $p->ruang->nama_ruang,
                $p->tanggal_pinjam,
                $p->status,
            ]);
        }

        fclose($file);

        $this->info("Report exported: {$filename}");
        $this->info("Total records: {$peminjaman->count()}");

        return Command::SUCCESS;
    }
}
```

**Usage:**
```bash
php artisan report:export-monthly     # Current month
php artisan report:export-monthly 11 2025  # November 2025
```
</details>

---

## ✅ Checklist Pemahaman

- [ ] Bisa membuat artisan command baru
- [ ] Bisa setup scheduled tasks dengan cron
- [ ] Bisa implementasi interactive forms dengan JavaScript
- [ ] Bisa membuat responsive tables
- [ ] Bisa implementasi modal dialogs
- [ ] Bisa styling dengan CSS (hover effects, animations)
- [ ] Bisa menggunakan icons dan badges
- [ ] Bisa test commands via tinker

---

## 📚 Referensi

1. **Laravel Artisan**: https://laravel.com/docs/11.x/artisan
2. **Task Scheduling**: https://laravel.com/docs/11.x/scheduling
3. **Bootstrap Components**: https://getbootstrap.com/docs/5.3/components/
4. **Font Awesome**: https://fontawesome.com/icons

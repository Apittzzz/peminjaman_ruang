# 🚀 DEVELOPMENT GUIDE - Sistem Peminjaman Ruang

> Guide lengkap untuk developer yang akan maintain atau develop sistem ini

---

## 📋 DAFTAR ISI

1. [Setup Development Environment](#setup-development-environment)
2. [Naming Conventions](#naming-conventions)
3. [Code Standards](#code-standards)
4. [Testing Guidelines](#testing-guidelines)
5. [Git Workflow](#git-workflow)
6. [Troubleshooting](#troubleshooting)

---

## 🛠️ SETUP DEVELOPMENT ENVIRONMENT

### Prerequisites
- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Node.js >= 18.x
- NPM atau Yarn

### Initial Setup

```bash
# 1. Clone repository
git clone https://github.com/Apittzzz/peminjaman_ruang.git
cd peminjaman_ruang

# 2. Install dependencies
composer install
npm install

# 3. Copy .env
cp .env.example .env

# 4. Generate app key
php artisan key:generate

# 5. Configure database di .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=peminjaman_ruang
DB_USERNAME=root
DB_PASSWORD=

# 6. Run migrations
php artisan migrate

# 7. Seed admin user
php artisan db:seed --class=AdminUserSeeder

# 8. Build assets
npm run dev

# 9. Run server
php artisan serve
```

### Development Tools

```bash
# Clear all cache
php artisan optimize:clear

# Clear specific cache
php artisan view:clear
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Generate IDE helper (optional)
composer require --dev barryvdh/laravel-ide-helper
php artisan ide-helper:generate
php artisan ide-helper:models

# Run tests
php artisan test
```

---

## 📝 NAMING CONVENTIONS

### **1. Controllers**

**Pattern:** `{Entity}{Action}Controller`

```php
✅ Good:
- UserController
- PeminjamanController
- LaporanController

❌ Bad:
- Users
- bookingctrl
- report_controller
```

### **2. Models**

**Pattern:** Singular PascalCase

```php
✅ Good:
- User
- Peminjaman
- Ruang

❌ Bad:
- Users
- peminjaman
- ruangs
```

### **3. Migrations**

**Pattern:** `{action}_{table}_table`

```php
✅ Good:
- 2025_11_01_create_users_table.php
- 2025_11_02_add_status_to_peminjaman_table.php

❌ Bad:
- create_user.php
- peminjaman.php
```

### **4. Views**

**Pattern:** lowercase dengan dot notation

```php
✅ Good:
- admin.users.index
- peminjam.peminjaman.create
- dashboard.admin

❌ Bad:
- AdminUsersIndex
- peminjam_peminjaman_create
```

### **5. Routes**

**Pattern:** kebab-case

```php
✅ Good:
Route::get('/admin/laporan-peminjaman', ...);
Route::post('/peminjam/ajukan-peminjaman', ...);

❌ Bad:
Route::get('/admin/laporanPeminjaman', ...);
Route::post('/peminjam/ajukan_peminjaman', ...);
```

### **6. Variables & Functions**

**Pattern:** camelCase

```php
✅ Good:
$peminjamanData
$startDate
function getPendingBookings()

❌ Bad:
$PeminjamanData
$start_date
function get_pending_bookings()
```

### **7. Database Tables & Columns**

**Pattern:** snake_case

```sql
✅ Good:
- users
- peminjaman
- tanggal_pinjam
- id_user

❌ Bad:
- Users
- Peminjaman
- tanggalPinjam
- idUser
```

---

## 💻 CODE STANDARDS

### **1. PHP Standards (PSR-12)**

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * User Management Controller
 * 
 * Handles CRUD operations for users
 */
class UserController extends Controller
{
    /**
     * Display list of users
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.users.index', compact('users'));
    }
    
    /**
     * Store new user
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|unique:users|max:255',
            'nama' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,petugas,peminjam',
        ]);
        
        $validated['password'] = bcrypt($validated['password']);
        
        User::create($validated);
        
        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan');
    }
}
```

### **2. Blade Templates**

```blade
@extends('layouts.app')

@section('title', 'Daftar User')

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('admin.users.index') }}">User</a>
</li>
<li class="breadcrumb-item active">Daftar</li>
@endsection

@section('content')
<div class="container-fluid py-4">
    {{-- Alert Success --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    {{-- Content Card --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-users me-2"></i>Daftar User
            </h5>
        </div>
        <div class="card-body">
            {{-- Table --}}
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Nama</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->nama }}</td>
                            <td>
                                <span class="badge bg-primary">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.users.edit', $user->id) }}" 
                                   class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                Tidak ada data
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            {{-- Pagination --}}
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
```

### **3. JavaScript**

```javascript
/**
 * Validate booking time range
 * 
 * @param {string} startDate - Start date (YYYY-MM-DD)
 * @param {string} endDate - End date (YYYY-MM-DD)
 * @param {string} startTime - Start time (HH:mm)
 * @param {string} endTime - End time (HH:mm)
 * @returns {boolean}
 */
function validateTimeRange(startDate, endDate, startTime, endTime) {
    // Same day booking - end time must be after start time
    if (startDate === endDate) {
        const start = new Date(`${startDate} ${startTime}`);
        const end = new Date(`${endDate} ${endTime}`);
        
        if (end <= start) {
            alert('Waktu selesai harus lebih besar dari waktu mulai');
            return false;
        }
    }
    
    return true;
}

// Event listener
document.getElementById('bookingForm').addEventListener('submit', function(e) {
    const startDate = document.getElementById('tanggal_pinjam').value;
    const endDate = document.getElementById('tanggal_kembali').value;
    const startTime = document.getElementById('jam_mulai').value;
    const endTime = document.getElementById('jam_selesai').value;
    
    if (!validateTimeRange(startDate, endDate, startTime, endTime)) {
        e.preventDefault();
    }
});
```

### **4. CSS**

```css
/* ============================================
   COMPONENT NAME - Description
   ============================================ */

/* Main container */
.action-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    background: white;
    padding: 2.5rem;
    margin-bottom: 1rem;
}

/* Hover state */
.action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
}

/* Icon styling */
.action-card .fas {
    color: var(--navy);
    margin-bottom: 1.5rem;
}

/* Responsive */
@media (max-width: 768px) {
    .action-card {
        padding: 1.5rem;
    }
}
```

---

## 🧪 TESTING GUIDELINES

### **1. Feature Test Example**

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function user_can_create_booking()
    {
        $user = User::factory()->create(['role' => 'peminjam']);
        
        $response = $this->actingAs($user)
            ->post('/peminjam/peminjaman', [
                'id_ruang' => 1,
                'tanggal_pinjam' => '2025-11-15',
                'tanggal_kembali' => '2025-11-15',
                'jam_mulai' => '08:00',
                'jam_selesai' => '10:00',
                'keperluan' => 'Meeting',
            ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('peminjaman', [
            'id_user' => $user->id,
            'keperluan' => 'Meeting',
        ]);
    }
}
```

### **2. Running Tests**

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter BookingTest

# Run with coverage
php artisan test --coverage
```

---

## 🔄 GIT WORKFLOW

### **Branch Strategy**

```
main (production)
├── develop (staging)
│   ├── feature/booking-system
│   ├── feature/reporting
│   └── feature/room-relocation
└── hotfix/critical-bug
```

### **Commit Message Convention**

```bash
# Format: <type>(<scope>): <subject>

# Types:
feat:     # New feature
fix:      # Bug fix
docs:     # Documentation
style:    # Formatting, missing semicolons
refactor: # Code restructuring
test:     # Adding tests
chore:    # Maintenance

# Examples:
git commit -m "feat(booking): add multi-day booking validation"
git commit -m "fix(auth): resolve session timeout issue"
git commit -m "docs(readme): update installation guide"
git commit -m "refactor(controllers): add docblocks to all methods"
```

### **Workflow**

```bash
# 1. Create feature branch
git checkout -b feature/new-feature

# 2. Make changes & commit
git add .
git commit -m "feat: implement new feature"

# 3. Push to remote
git push origin feature/new-feature

# 4. Create Pull Request
# Review code → Merge to develop → Test → Merge to main
```

---

## 🐛 TROUBLESHOOTING

### **Common Issues**

#### 1. Session Timeout

```bash
# Problem: User logout unexpectedly
# Solution: Change session driver to database
php artisan session:table
php artisan migrate
# Update .env: SESSION_DRIVER=database
```

#### 2. CSRF Token Mismatch

```bash
# Problem: 419 Page Expired
# Solution:
# 1. Clear cache
php artisan config:clear
# 2. Check @csrf in forms
# 3. Check session configuration
```

#### 3. Class Not Found

```bash
# Problem: Class 'App\Models\X' not found
# Solution: Regenerate autoload
composer dump-autoload
```

#### 4. Migration Error

```bash
# Problem: Foreign key constraint fails
# Solution: Run migrations in order
php artisan migrate:fresh
php artisan db:seed
```

### **Debug Tools**

```php
// Use Laravel Debugbar (development only)
composer require barryvdh/laravel-debugbar --dev

// Dump and die
dd($variable);

// Dump without stopping
dump($variable);

// Log to file
\Log::info('Debug message', ['data' => $variable]);

// Query log
\DB::enableQueryLog();
// ... your queries ...
dd(\DB::getQueryLog());
```

---

## 📚 RESOURCES

### Laravel Documentation
- Official Docs: https://laravel.com/docs/11.x
- Laracasts: https://laracasts.com

### Tools
- Laravel Debugbar: https://github.com/barryvdh/laravel-debugbar
- Laravel IDE Helper: https://github.com/barryvdh/laravel-ide-helper
- PHPStan: https://phpstan.org

### Community
- Laravel Forum: https://laracasts.com/discuss
- Stack Overflow: https://stackoverflow.com/questions/tagged/laravel

---

**Happy Coding! 🚀**

*Last Updated: 2025-11-13*

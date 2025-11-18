# 📝 Coding Guidelines & Best Practices

## Daftar Isi
1. [PHP Coding Standards (PSR-12)](#1-php-coding-standards-psr-12)
2. [Laravel Best Practices](#2-laravel-best-practices)
3. [Security Best Practices](#3-security-best-practices)
4. [Code Documentation](#4-code-documentation)
5. [Git Workflow](#5-git-workflow)

---

## 1. PHP Coding Standards (PSR-12)

### 1.1 Naming Conventions

#### Classes
```php
// ✅ BENAR - PascalCase
class PeminjamanController extends Controller
class StorePeminjamanRequest extends FormRequest
class AdminMiddleware

// ❌ SALAH
class peminjaman_controller
class store_peminjaman_request
class admin_Middleware
```

#### Methods & Functions
```php
// ✅ BENAR - camelCase
public function store(Request $request)
public function checkAvailability()
private function validateTimeRange()

// ❌ SALAH
public function Store(Request $request)
public function check_availability()
private function Validate_time_range()
```

#### Variables
```php
// ✅ BENAR - camelCase
$userName = 'John';
$totalPeminjaman = 10;
$isAvailable = true;

// ❌ SALAH
$user_name = 'John';
$TotalPeminjaman = 10;
$Is_Available = true;
```

#### Constants
```php
// ✅ BENAR - UPPER_CASE
const MAX_UPLOAD_SIZE = 5242880;
const DEFAULT_TIMEOUT = 30;

// ❌ SALAH
const maxUploadSize = 5242880;
const default_timeout = 30;
```

---

### 1.2 Code Formatting

#### Indentation & Spacing
```php
// ✅ BENAR - 4 spaces indentation, proper spacing
class PeminjamanController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_ruang' => 'required',
            'tanggal_pinjam' => 'required|date',
        ]);

        if ($this->checkAvailability($data)) {
            return $this->createPeminjaman($data);
        }

        return back()->withErrors(['error' => 'Ruangan tidak tersedia']);
    }
}

// ❌ SALAH - No spacing, inconsistent indentation
class PeminjamanController extends Controller{
public function store(Request $request){
$data=$request->validate(['id_ruang'=>'required','tanggal_pinjam'=>'required|date']);
if($this->checkAvailability($data)){
return $this->createPeminjaman($data);}
return back()->withErrors(['error'=>'Ruangan tidak tersedia']);}}
```

#### Braces Placement
```php
// ✅ BENAR - Opening brace on same line for control structures
if ($condition) {
    // code
} elseif ($otherCondition) {
    // code
} else {
    // code
}

foreach ($items as $item) {
    // code
}

// ✅ BENAR - Opening brace on new line for classes/methods
class MyClass
{
    public function myMethod()
    {
        // code
    }
}
```

#### Line Length
```php
// ✅ BENAR - Max 120 characters, break long lines
$peminjaman = Peminjaman::with(['user', 'ruang'])
    ->where('status', 'approved')
    ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
    ->orderBy('created_at', 'desc')
    ->paginate(10);

// ❌ SALAH - Too long (>120 characters)
$peminjaman = Peminjaman::with(['user', 'ruang'])->where('status', 'approved')->whereBetween('tanggal_pinjam', [$startDate, $endDate])->orderBy('created_at', 'desc')->paginate(10);
```

---

### 1.3 Method Length & Complexity

#### Single Responsibility Principle
```php
// ✅ BENAR - Metode kecil, satu tanggung jawab
public function store(Request $request)
{
    $validatedData = $this->validateRequest($request);
    $peminjaman = $this->createPeminjaman($validatedData);
    $this->sendNotification($peminjaman);
    
    return redirect()->route('peminjaman.index')
        ->with('success', 'Peminjaman berhasil');
}

private function validateRequest(Request $request)
{
    return $request->validate([
        'id_ruang' => 'required|exists:ruang,id_ruang',
        'tanggal_pinjam' => 'required|date|after_or_equal:today',
        // ... more rules
    ]);
}

private function createPeminjaman(array $data)
{
    return Peminjaman::create([
        'id_user' => Auth::id(),
        ...$data,
        'status' => 'pending',
    ]);
}

private function sendNotification(Peminjaman $peminjaman)
{
    // Send notification logic
}

// ❌ SALAH - Metode terlalu panjang (100+ lines)
public function store(Request $request)
{
    // 20 lines validation
    // 30 lines business logic
    // 20 lines database operations
    // 15 lines notification
    // 15 lines error handling
    // Total: 100+ lines = HARD TO READ & MAINTAIN
}
```

---

### 1.4 Return Type Declarations

```php
// ✅ BENAR - Declare return types
public function store(Request $request): RedirectResponse
{
    // ...
    return redirect()->route('peminjaman.index');
}

public function show(int $id): View
{
    $peminjaman = Peminjaman::findOrFail($id);
    return view('peminjaman.show', compact('peminjaman'));
}

public function checkAvailability(int $ruangId, string $tanggal): bool
{
    return !Peminjaman::where('id_ruang', $ruangId)
        ->where('tanggal_pinjam', $tanggal)
        ->where('status', 'approved')
        ->exists();
}

// ❌ SALAH - No type declarations
public function store(Request $request)
{
    return redirect()->route('peminjaman.index');
}
```

---

## 2. Laravel Best Practices

### 2.1 Query Optimization (N+1 Prevention)

```php
// ❌ BAD - N+1 Query Problem
$peminjaman = Peminjaman::all(); // 1 query
foreach ($peminjaman as $p) {
    echo $p->user->nama;  // N queries (1 per item)
    echo $p->ruang->nama_ruang;  // N queries
}
// Total: 1 + N + N = 2N + 1 queries

// ✅ GOOD - Eager Loading
$peminjaman = Peminjaman::with(['user', 'ruang'])->get(); // 3 queries only
foreach ($peminjaman as $p) {
    echo $p->user->nama;  // No additional query
    echo $p->ruang->nama_ruang;  // No additional query
}
// Total: 3 queries (peminjaman, users, ruang)
```

#### Conditional Eager Loading
```php
// Load relation only if needed
$query = Peminjaman::query();

if ($request->has('include_user')) {
    $query->with('user');
}

if ($request->has('include_ruang')) {
    $query->with('ruang');
}

$peminjaman = $query->get();
```

---

### 2.2 Form Request Validation

```php
// ✅ GOOD - Separate validation class
// app/Http/Requests/StorePeminjamanRequest.php
class StorePeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'peminjam';
    }

    public function rules(): array
    {
        return [
            'id_ruang' => 'required|exists:ruang,id_ruang',
            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'required|date_format:H:i',
            'keperluan' => 'required|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'id_ruang.required' => 'Ruangan harus dipilih',
            'tanggal_pinjam.after_or_equal' => 'Tanggal tidak boleh lampau',
        ];
    }
}

// Controller - Clean & simple
public function store(StorePeminjamanRequest $request)
{
    Peminjaman::create($request->validated());
    return redirect()->route('peminjaman.index');
}

// ❌ BAD - Validation in controller
public function store(Request $request)
{
    // 50 lines of validation rules
    $request->validate([
        // ... many rules
    ]);
    
    // Business logic
}
```

---

### 2.3 Route Organization

```php
// ✅ GOOD - Grouped by middleware and prefix
// routes/web.php

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Admin routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('ruang', RuangController::class);
        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    });
    
    // Peminjam routes
    Route::middleware('peminjam')->prefix('peminjam')->name('peminjam.')->group(function () {
        Route::resource('peminjaman', PeminjamanController::class);
    });
});

// ❌ BAD - Unorganized, repetitive
Route::get('/admin/users', [UserController::class, 'index'])->middleware('auth', 'admin');
Route::get('/admin/users/create', [UserController::class, 'create'])->middleware('auth', 'admin');
Route::post('/admin/users', [UserController::class, 'store'])->middleware('auth', 'admin');
// ... repeat for every route
```

---

### 2.4 Database Transaction

```php
// ✅ GOOD - Use transaction for multiple operations
use Illuminate\Support\Facades\DB;

public function approve($id)
{
    try {
        DB::beginTransaction();
        
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->update(['status' => 'approved']);
        
        $peminjaman->ruang->update(['status' => 'dipakai']);
        
        // Send notification
        $this->notifyUser($peminjaman);
        
        DB::commit();
        
        return redirect()->back()->with('success', 'Berhasil disetujui');
        
    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('Approval failed', [
            'error' => $e->getMessage(),
            'peminjaman_id' => $id,
        ]);
        
        return redirect()->back()->with('error', 'Gagal menyetujui');
    }
}

// ❌ BAD - No transaction, inconsistent state if error
public function approve($id)
{
    $peminjaman = Peminjaman::findOrFail($id);
    $peminjaman->update(['status' => 'approved']);
    
    // If this fails, peminjaman already approved but room still kosong
    $peminjaman->ruang->update(['status' => 'dipakai']);
}
```

---

### 2.5 Eloquent Accessors & Mutators

```php
// ✅ GOOD - Use accessors for computed attributes
class Peminjaman extends Model
{
    // Accessor - Transform when reading
    public function getFormattedTanggalPinjamAttribute(): string
    {
        return Carbon::parse($this->tanggal_pinjam)->format('d/m/Y');
    }
    
    // Usage in view
    {{ $peminjaman->formatted_tanggal_pinjam }}
    
    // Mutator - Transform when writing
    public function setKeperluan Attribute(string $value): void
    {
        $this->attributes['keperluan'] = ucfirst(trim($value));
    }
}

// ❌ BAD - Format in view/controller repeatedly
// View
{{ Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') }}
// Controller
$tanggal = Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y');
```

---

## 3. Security Best Practices

### 3.1 SQL Injection Prevention

```php
// ✅ GOOD - Query Builder (parameterized queries)
$users = DB::table('users')
    ->where('username', $request->username)
    ->first();

// ✅ GOOD - Eloquent ORM
$users = User::where('username', $request->username)->first();

// ❌ BAD - Raw SQL with concatenation
$users = DB::select("SELECT * FROM users WHERE username = '{$request->username}'");
// Vulnerable to: ' OR '1'='1
```

---

### 3.2 XSS Prevention

```blade
<!-- ✅ GOOD - Blade auto-escaping -->
<p>{{ $user->nama }}</p>
<p>{{ $peminjaman->keperluan }}</p>

<!-- Output: &lt;script&gt;alert('XSS')&lt;/script&gt; -->

<!-- ❌ BAD - Raw output -->
<p>{!! $user->nama !!}</p>
<!-- Vulnerable to: <script>alert('XSS')</script> -->

<!-- ✅ GOOD - Use raw only for trusted content -->
<div class="content">
    {!! $trustedHtmlFromAdmin !!}
</div>
```

---

### 3.3 CSRF Protection

```blade
<!-- ✅ GOOD - Always include @csrf in forms -->
<form method="POST" action="{{ route('peminjaman.store') }}">
    @csrf
    <input type="text" name="keperluan">
    <button type="submit">Submit</button>
</form>

<!-- ❌ BAD - No CSRF token -->
<form method="POST" action="{{ route('peminjaman.store') }}">
    <input type="text" name="keperluan">
    <button type="submit">Submit</button>
</form>
<!-- Will get 419 error -->
```

---

### 3.4 Password Hashing

```php
// ✅ GOOD - bcrypt hashing
use Illuminate\Support\Facades\Hash;

// Store password
$user->password = Hash::make($request->password);

// Verify password
if (Hash::check($request->password, $user->password)) {
    // Password correct
}

// ❌ BAD - Plain text or weak hashing
$user->password = $request->password; // Never!
$user->password = md5($request->password); // Weak!
```

---

### 3.5 Mass Assignment Protection

```php
// ✅ GOOD - Define fillable/guarded
class User extends Model
{
    protected $fillable = [
        'username',
        'nama',
        'role',
    ];
    
    // Or use guarded
    protected $guarded = ['id_user'];
}

// ❌ BAD - No protection
class User extends Model
{
    // No $fillable or $guarded
}

// Vulnerable to:
User::create($request->all());
// Attacker can send: is_admin=1, credits=9999999
```

---

### 3.6 Authorization (Gates & Policies)

```php
// ✅ GOOD - Use gates for authorization
// app/Providers/AuthServiceProvider.php
Gate::define('approve-peminjaman', function (User $user) {
    return in_array($user->role, ['admin', 'petugas']);
});

// Controller
public function approve($id)
{
    if (!Gate::allows('approve-peminjaman')) {
        abort(403);
    }
    
    // Approve logic
}

// Blade
@can('approve-peminjaman')
    <button>Approve</button>
@endcan
```

---

## 4. Code Documentation

### 4.1 PHPDoc Comments

```php
/**
 * Store a newly created peminjaman in storage.
 *
 * This method validates the request, checks room availability,
 * creates the peminjaman record with status 'pending', and
 * redirects to the index page.
 *
 * @param  StorePeminjamanRequest  $request  The validated request data
 * @return \Illuminate\Http\RedirectResponse
 * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
 */
public function store(StorePeminjamanRequest $request): RedirectResponse
{
    // Implementation
}

/**
 * Check if a room is available for booking.
 *
 * @param  int     $ruangId       The room ID to check
 * @param  string  $tanggalPinjam The date to check (Y-m-d format)
 * @param  string  $waktuMulai    Start time (H:i format)
 * @param  string  $waktuSelesai  End time (H:i format)
 * @return bool    True if available, false otherwise
 */
private function checkAvailability(
    int $ruangId,
    string $tanggalPinjam,
    string $waktuMulai,
    string $waktuSelesai
): bool {
    // Implementation
}
```

---

### 4.2 Inline Comments

```php
// ✅ GOOD - Explain WHY, not WHAT
public function approve($id)
{
    $peminjaman = Peminjaman::findOrFail($id);
    
    // Only pending status can be approved to maintain data integrity
    if ($peminjaman->status !== 'pending') {
        return back()->with('error', 'Hanya status pending yang bisa disetujui');
    }
    
    // Update room status to prevent double booking
    $peminjaman->ruang->update(['status' => 'dipakai']);
    
    // Mark as approved
    $peminjaman->update(['status' => 'approved']);
}

// ❌ BAD - State the obvious
public function approve($id)
{
    // Find peminjaman by id
    $peminjaman = Peminjaman::findOrFail($id);
    
    // Check if status is pending
    if ($peminjaman->status !== 'pending') {
        // Return back with error
        return back()->with('error', 'Hanya status pending yang bisa disetujui');
    }
    
    // Update ruang status
    $peminjaman->ruang->update(['status' => 'dipakai']);
}
```

---

### 4.3 README Documentation

```markdown
## Installation

### Requirements
- PHP >= 8.2
- Composer
- MySQL >= 8.0

### Steps
1. Clone repository
   ```bash
   git clone https://github.com/user/project.git
   cd project
   ```

2. Install dependencies
   ```bash
   composer install
   npm install && npm run build
   ```

3. Setup environment
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure database in `.env`
   ```env
   DB_DATABASE=peminjaman_ruang
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Run migrations
   ```bash
   php artisan migrate --seed
   ```

6. Start server
   ```bash
   php artisan serve
   ```

## Default Credentials
- Admin: `admin` / `admin123`
- Petugas: `petugas1` / `petugas123`
- Peminjam: `peminjam1` / `peminjam123`
```

---

## 5. Git Workflow

### 5.1 Commit Messages

```bash
# ✅ GOOD - Clear and descriptive (Conventional Commits)
git commit -m "feat: add room availability check API endpoint"
git commit -m "fix: resolve N+1 query in jadwal controller"
git commit -m "refactor: extract validation to form request"
git commit -m "docs: update README with installation steps"
git commit -m "style: format code with PSR-12 standards"
git commit -m "test: add unit tests for peminjaman service"

# ❌ BAD - Vague
git commit -m "update"
git commit -m "fix bug"
git commit -m "changes"
git commit -m "wip"
```

**Format:**
```
<type>(<scope>): <subject>

<body>

<footer>
```

**Types:**
- `feat`: New feature
- `fix`: Bug fix
- `refactor`: Code refactoring
- `docs`: Documentation
- `style`: Code formatting
- `test`: Adding tests
- `chore`: Maintenance tasks

---

### 5.2 Branch Strategy

```bash
# Main branches
main       # Production-ready code
develop    # Development branch

# Feature branches
feature/user-management
feature/room-booking
feature/approval-system

# Bugfix branches
bugfix/login-redirect-issue
bugfix/date-validation

# Hotfix branches (for production)
hotfix/security-patch
hotfix/csrf-token-issue
```

**Workflow:**
```bash
# Create feature branch from develop
git checkout develop
git pull origin develop
git checkout -b feature/new-feature

# Work on feature
git add .
git commit -m "feat: implement new feature"

# Push to remote
git push origin feature/new-feature

# Create pull request: feature/new-feature → develop

# After merge, delete branch
git branch -d feature/new-feature
```

---

### 5.3 .gitignore

```gitignore
# Laravel specific
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.phpunit.result.cache
Homestead.json
Homestead.yaml
npm-debug.log
yarn-error.log

# IDE
/.vscode
/.idea
*.swp
*.swo
*~

# OS
.DS_Store
Thumbs.db
```

---

## 🎯 Code Review Checklist

### Before Commit:
- [ ] Code follows PSR-12 standards
- [ ] No commented-out code
- [ ] No console.log() or dd() left
- [ ] Variables have meaningful names
- [ ] Methods are small (<30 lines)
- [ ] No duplicate code (DRY principle)
- [ ] Error handling implemented
- [ ] Security measures in place (CSRF, XSS, SQL injection)
- [ ] Database queries optimized (no N+1)
- [ ] PHPDoc comments for public methods

### Before Push:
- [ ] All tests passing
- [ ] No merge conflicts
- [ ] Commit messages are clear
- [ ] .env not included
- [ ] No sensitive data in code

### Before Merge (Pull Request):
- [ ] Code reviewed by peer
- [ ] No breaking changes
- [ ] Documentation updated
- [ ] Migration files included (if DB changes)
- [ ] Backward compatible

---

## ✅ Checklist Pemahaman

- [ ] Memahami PSR-12 naming conventions
- [ ] Bisa implement Form Request Validation
- [ ] Bisa prevent N+1 query dengan eager loading
- [ ] Memahami SQL injection, XSS, CSRF prevention
- [ ] Bisa menulis PHPDoc comments
- [ ] Bisa menggunakan Git dengan baik
- [ ] Bisa refactor code untuk maintainability
- [ ] Memahami SOLID principles

---

## 📚 Referensi

1. **PSR-12**: https://www.php-fig.org/psr/psr-12/
2. **Laravel Best Practices**: https://github.com/alexeymezenin/laravel-best-practices
3. **OWASP Security**: https://owasp.org/www-project-top-ten/
4. **Conventional Commits**: https://www.conventionalcommits.org/
5. **Clean Code**: Robert C. Martin

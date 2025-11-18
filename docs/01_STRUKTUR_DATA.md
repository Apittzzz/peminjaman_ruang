# 📊 Dokumentasi Struktur Data

## Daftar Isi
1. [Array dan Collection](#1-array-dan-collection)
2. [Hash Map / Associative Array](#2-hash-map--associative-array)
3. [Relational Data Structure](#3-relational-data-structure)
4. [Queue dan Stack](#4-queue-dan-stack)
5. [Tree Structure](#5-tree-structure)

---

## 1. Array dan Collection

### 1.1 Filter Data dengan Collection

**Lokasi File:** `app/Http/Controllers/JadwalController.php`

```php
// Filter ruangan berdasarkan status
$ruangs = Ruang::with(['peminjamanAktif', 'penggunaDefault'])->get();

if ($statusFilter !== 'all') {
    $ruangs = $ruangs->filter(function($ruang) use ($statusFilter) {
        return $ruang->status === $statusFilter;
    });
}
```

**Penjelasan:**
- **Input**: Collection of Ruang objects
- **Process**: Filter berdasarkan kondisi (status ruangan)
- **Output**: Filtered Collection
- **Kompleksitas Waktu**: O(n) - Linear time (harus cek setiap elemen)
- **Keuntungan**: Readable, chainable, built-in Laravel

### 1.2 Sorting Data

**Lokasi File:** `app/Http/Controllers/Peminjam/PeminjamanController.php`

```php
// Sort peminjaman by tanggal created (descending)
$peminjaman = Peminjaman::with('ruang')
    ->where('id_user', Auth::id())
    ->orderBy('created_at', 'desc')
    ->get();
```

**Penjelasan:**
- **Input**: Unsorted collection
- **Process**: Sort descending by created_at field
- **Output**: Sorted collection (terbaru di atas)
- **Algoritma**: Database index sorting (optimal)
- **Alternative**: `$collection->sortByDesc('created_at')` (in-memory)

### 1.3 Grouping Data

**Lokasi File:** `app/Http/Controllers/Admin/LaporanController.php`

```php
// Group peminjaman by status
$peminjaman->groupBy('status');

// Count by status
$stats = [
    'pending' => $peminjaman->where('status', 'pending')->count(),
    'approved' => $peminjaman->where('status', 'approved')->count(),
    'rejected' => $peminjaman->where('status', 'rejected')->count(),
];
```

**Penjelasan:**
- **Struktur**: Grouped array dengan key = status, value = collection items
- **Use Case**: Statistik, reporting, data aggregation
- **Kompleksitas**: O(n) untuk grouping

### 1.4 Map dan Transform

```php
// Transform data untuk chart
$ruangStats = Peminjaman::select('id_ruang', DB::raw('count(*) as total'))
    ->groupBy('id_ruang')
    ->with('ruang')
    ->get()
    ->map(function($item) {
        return [
            'nama' => $item->ruang->nama_ruang,
            'total' => $item->total
        ];
    });
```

**Penjelasan:**
- **Input**: Collection dengan struktur kompleks
- **Process**: Transform ke struktur lebih sederhana
- **Output**: Array dengan key yang diinginkan
- **Use Case**: Prepare data untuk frontend/chart

---

## 2. Hash Map / Associative Array

### 2.1 Status Color Mapping

**Lokasi File:** `resources/views/peminjam/peminjaman/show.blade.php`

```php
$statusColors = [
    'pending' => 'warning',
    'approved' => 'success',
    'rejected' => 'danger',
    'cancelled' => 'secondary',
    'selesai' => 'info'
];

// Access
$color = $statusColors[$peminjaman->status];
```

**Penjelasan:**
- **Struktur**: Key-value pairs (status → color)
- **Access Time**: O(1) - Constant time (hash table)
- **Use Case**: Mapping enum values ke display properties
- **Keuntungan**: Fast lookup, maintainable, centralized

### 2.2 Statistics Aggregation

**Lokasi File:** `app/Http/Controllers/Admin/LaporanController.php`

```php
$stats = [
    'total_peminjaman' => $peminjaman->count(),
    'pending' => $peminjaman->where('status', 'pending')->count(),
    'approved' => $peminjaman->where('status', 'approved')->count(),
    'rejected' => $peminjaman->where('status', 'rejected')->count(),
    'selesai' => $peminjaman->where('status', 'selesai')->count(),
    'cancelled' => $peminjaman->where('status', 'cancelled')->count(),
];
```

**Penjelasan:**
- **Struktur**: Associative array dengan key descriptive
- **Use Case**: Dashboard statistics, reporting
- **Benefit**: Self-documenting, easy to access

### 2.3 Validation Rules

**Lokasi File:** `app/Http/Requests/StorePeminjamanRequest.php`

```php
return [
    'id_ruang' => 'required|exists:ruang,id_ruang',
    'tanggal_pinjam' => 'required|date|after_or_equal:today',
    'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
    'waktu_mulai' => 'required|date_format:H:i',
    'waktu_selesai' => 'required|date_format:H:i',
    'keperluan' => 'required|string|max:500',
];
```

**Penjelasan:**
- **Struktur**: Field name → Validation rules string
- **Processing**: Laravel parses dan validates
- **Benefit**: Declarative, readable, maintainable

---

## 3. Relational Data Structure

### 3.1 One-to-Many Relationship

**Lokasi File:** `app/Models/User.php`

```php
class User extends Authenticatable
{
    /**
     * User has many Peminjaman
     * 1 User → N Peminjaman
     */
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'id_user', 'id_user');
    }
}
```

**Diagram:**
```
User (1)
  ├── Peminjaman 1
  ├── Peminjaman 2
  ├── Peminjaman 3
  └── Peminjaman ...n
```

**Penjelasan:**
- **Foreign Key**: `id_user` di tabel `peminjaman`
- **Query**: `$user->peminjaman` → returns Collection
- **Use Case**: Get all bookings by a user

### 3.2 Many-to-One (Inverse)

**Lokasi File:** `app/Models/Peminjaman.php`

```php
class Peminjaman extends Model
{
    /**
     * Peminjaman belongs to User
     * N Peminjaman → 1 User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Peminjaman belongs to Ruang
     * N Peminjaman → 1 Ruang
     */
    public function ruang()
    {
        return $this->belongsTo(Ruang::class, 'id_ruang', 'id_ruang');
    }
}
```

**Diagram:**
```
Peminjaman
  ├── belongsTo → User
  └── belongsTo → Ruang
```

**Penjelasan:**
- **Foreign Keys**: `id_user`, `id_ruang`
- **Query**: `$peminjaman->user` → returns User model
- **Query**: `$peminjaman->ruang` → returns Ruang model

### 3.3 Eager Loading (N+1 Prevention)

**Lokasi File:** `app/Http/Controllers/JadwalController.php`

```php
// ❌ BAD - N+1 Query Problem
$ruangs = Ruang::all(); // 1 query
foreach ($ruangs as $ruang) {
    echo $ruang->peminjaman->count(); // N queries (1 per ruang)
}
// Total: 1 + N queries

// ✅ GOOD - Eager Loading
$ruangs = Ruang::with('peminjaman')->get(); // 2 queries only
foreach ($ruangs as $ruang) {
    echo $ruang->peminjaman->count(); // No additional query
}
// Total: 2 queries
```

**Penjelasan:**
- **Problem**: N+1 query menyebabkan slow performance
- **Solution**: Use `with()` untuk eager load relationships
- **Benefit**: Significant performance improvement
- **Query Count**: 1 (main) + 1 (relation) = 2 queries total

### 3.4 Nested Relationships

```php
// Load ruang dengan peminjaman dan user peminjam
$ruangs = Ruang::with(['peminjaman.user'])->get();

// Access
foreach ($ruangs as $ruang) {
    foreach ($ruang->peminjaman as $p) {
        echo $p->user->nama; // No N+1 query
    }
}
```

**Penjelasan:**
- **Syntax**: Dot notation untuk nested relations
- **Queries**: 3 total (ruang, peminjaman, users)
- **Without eager loading**: 1 + N + M queries

---

## 4. Queue dan Stack

### 4.1 Session Data (FIFO Queue)

**Lokasi File:** `app/Http/Controllers/Auth/AuthenticatedSessionController.php`

```php
// Push to session (Enqueue)
session(['user_id' => $user->id_user]);
session(['last_activity' => now()]);

// Access (Peek)
$userId = session('user_id');

// Remove (Dequeue)
session()->forget('user_id');

// Clear all (Flush)
session()->flush();
```

**Penjelasan:**
- **Struktur**: FIFO (First In, First Out)
- **Storage**: File-based atau database
- **Use Case**: User authentication state
- **Lifetime**: Configurable (default 120 minutes)

### 4.2 Flash Messages (Stack-like)

```php
// Push message (Controller)
return redirect()->route('peminjaman.index')
    ->with('success', 'Peminjaman berhasil diajukan');

// Pop message (View - auto removed after display)
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
```

**Penjelasan:**
- **Behavior**: Auto-removed after first access (stack pop)
- **Use Case**: One-time notifications
- **Storage**: Session with `flashdata` flag

---

## 5. Tree Structure

### 5.1 Navigation Menu (Hierarchical)

**Lokasi File:** `resources/views/layouts/app.blade.php`

```
Application Root
│
├── Dashboard
│
├── Admin Menu (role = admin)
│   ├── Manajemen User
│   │   ├── Lihat Semua User
│   │   ├── Tambah User
│   │   └── Edit User
│   │
│   ├── Manajemen Ruangan
│   │   ├── Lihat Semua Ruangan
│   │   ├── Tambah Ruangan
│   │   └── Edit Ruangan
│   │
│   ├── Persetujuan Peminjaman
│   │   ├── List Pending
│   │   └── Approve/Reject
│   │
│   └── Laporan
│       ├── View Report
│       └── Export Excel
│
├── Petugas Menu (role = petugas)
│   ├── Persetujuan Peminjaman
│   └── Jadwal Ruangan
│
└── Peminjam Menu (role = peminjam)
    ├── Ajukan Peminjaman
    ├── Riwayat Peminjaman
    └── Jadwal Ruangan
```

**Implementation:**

```blade
@if(Auth::user()->role === 'admin')
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle">Manajemen</a>
        <ul class="dropdown-menu">
            <li><a href="{{ route('admin.users.index') }}">User</a></li>
            <li><a href="{{ route('admin.ruang.index') }}">Ruangan</a></li>
        </ul>
    </li>
@endif
```

**Penjelasan:**
- **Struktur**: Tree dengan role-based nodes
- **Depth**: 2-3 levels (root → category → item)
- **Traversal**: Role check untuk prune branches
- **Use Case**: Dynamic menu rendering

### 5.2 Folder Structure (File System Tree)

```
app/
├── Console/
│   └── Commands/
│       ├── MarkFinishedPeminjaman.php
│       └── RefreshRuangStatus.php
│
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── LaporanController.php
│   │   │   ├── PersetujuanController.php
│   │   │   └── RuangController.php
│   │   │
│   │   ├── Auth/
│   │   │   └── AuthenticatedSessionController.php
│   │   │
│   │   └── Peminjam/
│   │       └── PeminjamanController.php
│   │
│   ├── Middleware/
│   │   ├── AdminMiddleware.php
│   │   ├── Peminjam.php
│   │   └── PetugasMiddleware.php
│   │
│   └── Requests/
│       └── StorePeminjamanRequest.php
│
└── Models/
    ├── Peminjaman.php
    ├── Ruang.php
    └── User.php
```

**Penjelasan:**
- **Organization**: Modular by feature/role
- **Benefit**: Easy navigation, separation of concerns
- **Standard**: PSR-4 autoloading

---

## 🎯 Latihan Soal

### Soal 1: Array Filtering
**Q:** Bagaimana cara filter peminjaman yang statusnya 'approved' dan tanggal pinjam adalah hari ini?

<details>
<summary>Jawaban</summary>

```php
$today = now()->format('Y-m-d');
$approvedToday = Peminjaman::where('status', 'approved')
    ->whereDate('tanggal_pinjam', $today)
    ->get();

// Atau dengan Collection
$approvedToday = $peminjaman->filter(function($p) use ($today) {
    return $p->status === 'approved' && $p->tanggal_pinjam === $today;
});
```
</details>

### Soal 2: Hash Map Lookup
**Q:** Buatlah mapping untuk role ke dashboard route yang sesuai.

<details>
<summary>Jawaban</summary>

```php
$dashboardRoutes = [
    'admin' => 'dashboard.admin',
    'petugas' => 'dashboard.petugas',
    'peminjam' => 'dashboard.peminjam',
];

$route = $dashboardRoutes[Auth::user()->role];
return redirect()->route($route);
```
</details>

### Soal 3: Relational Query
**Q:** Bagaimana query untuk mendapatkan semua user yang pernah meminjam ruang tertentu?

<details>
<summary>Jawaban</summary>

```php
// Via Ruang model
$ruang = Ruang::find($id);
$users = $ruang->peminjaman()
    ->with('user')
    ->get()
    ->pluck('user')
    ->unique('id_user');

// Direct query
$users = User::whereHas('peminjaman', function($query) use ($ruangId) {
    $query->where('id_ruang', $ruangId);
})->get();
```
</details>

---

## 📚 Referensi

1. **Laravel Collections**: https://laravel.com/docs/11.x/collections
2. **Eloquent Relationships**: https://laravel.com/docs/11.x/eloquent-relationships
3. **Query Builder**: https://laravel.com/docs/11.x/queries
4. **Data Structures in PHP**: https://www.php.net/manual/en/book.ds.php

---

## ✅ Checklist Pemahaman

- [ ] Bisa jelaskan perbedaan array dan collection
- [ ] Bisa implementasi eager loading
- [ ] Bisa membuat hash map untuk mapping data
- [ ] Bisa query relational data dengan Eloquent
- [ ] Bisa optimasi query untuk avoid N+1 problem
- [ ] Bisa jelaskan kompleksitas waktu operasi array (filter, sort, map)

# 📋 Spesifikasi Program - Sistem Peminjaman Ruangan

## Daftar Isi
1. [Requirement Specification](#1-requirement-specification)
2. [Data Specification](#2-data-specification)
3. [API Specification](#3-api-specification)
4. [Business Logic Specification](#4-business-logic-specification)
5. [Validation Specification](#5-validation-specification)

---

## 1. Requirement Specification

### 1.1 Functional Requirements

#### FR-001: User Authentication
**Requirement:** Sistem harus dapat melakukan autentikasi user dengan 3 role berbeda.

**Specification:**
- **Input**: 
  - Username (string, required, max 50 chars)
  - Password (string, required, min 8 chars)
- **Process**:
  1. Validate credentials dari database
  2. Check password dengan bcrypt hash
  3. Create session jika valid
  4. Regenerate session ID (security)
- **Output**:
  - Success: Redirect ke dashboard sesuai role
  - Failure: Error message "Invalid credentials"
- **Error Handling**:
  - Username tidak ditemukan → "Invalid credentials"
  - Password salah → "Invalid credentials" (same message, security)
  - Account inactive → "Account disabled"

**Implementation:** `app/Http/Controllers/Auth/AuthenticatedSessionController.php`

```php
public function store(LoginRequest $request): RedirectResponse
{
    try {
        Log::info('Login attempt started', [
            'session_id' => session()->getId(),
            'username' => $request->only('username'),
        ]);

        $request->authenticate();
        
        Log::info('Authentication successful');
        
        $request->session()->regenerate();
        
        $user = Auth::user();
        
        $redirectUrl = match ($user->role) {
            'admin' => route('dashboard.admin'),
            'petugas' => route('dashboard.petugas'),
            'peminjam' => route('dashboard.peminjam'),
            default => route('dashboard'),
        };
        
        return redirect()->intended($redirectUrl);
        
    } catch (\Exception $e) {
        Log::error('Login error:', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
        
        return back()->withErrors([
            'username' => 'Login failed. Please try again.',
        ]);
    }
}
```

---

#### FR-002: Peminjaman Management (Create)
**Requirement:** Peminjam dapat mengajukan peminjaman ruangan dengan validasi availability.

**Specification:**
- **Input**:
  - id_ruang (integer, required, exists in ruang table)
  - tanggal_pinjam (date, required, >= today)
  - tanggal_kembali (date, required, >= tanggal_pinjam)
  - waktu_mulai (time, required, format HH:MM)
  - waktu_selesai (time, required, > waktu_mulai if same date)
  - keperluan (text, required, max 500 chars)

- **Business Rules**:
  1. Tidak boleh booking tanggal lampau
  2. Waktu selesai harus > waktu mulai (jika hari sama)
  3. Ruangan tidak boleh bentrok dengan booking lain
  4. User hanya bisa booking jika status peminjam
  5. Default status = 'pending'

- **Process**:
  1. Validate all input fields
  2. Check room availability (no conflicts)
  3. Check date/time validity
  4. Create peminjaman record
  5. Set status = 'pending'
  6. (Optional) Send notification to admin

- **Output**:
  - Success: Redirect to index with success message
  - Failure: Back to form with error messages

**Implementation:** `app/Http/Controllers/Peminjam/PeminjamanController.php`

```php
public function store(Request $request)
{
    $request->validate([
        'id_ruang' => 'required|exists:ruang,id_ruang',
        'tanggal_pinjam' => 'required|date|after_or_equal:today',
        'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
        'waktu_mulai' => 'required|date_format:H:i',
        'waktu_selesai' => 'required|date_format:H:i',
        'keperluan' => 'required|string|max:500',
    ]);

    // Check time validity for same date
    if ($request->tanggal_pinjam === $request->tanggal_kembali) {
        if ($request->waktu_selesai <= $request->waktu_mulai) {
            return back()->withErrors([
                'waktu_selesai' => 'Waktu selesai harus lebih besar dari waktu mulai'
            ])->withInput();
        }
    }

    // Check room availability
    $conflict = Peminjaman::where('id_ruang', $request->id_ruang)
        ->where('status', 'approved')
        ->where('tanggal_pinjam', '<=', $request->tanggal_kembali)
        ->where('tanggal_kembali', '>=', $request->tanggal_pinjam)
        ->exists();

    if ($conflict) {
        return back()->withErrors([
            'id_ruang' => 'Ruangan tidak tersedia pada tanggal tersebut'
        ])->withInput();
    }

    // Create peminjaman
    Peminjaman::create([
        'id_user' => Auth::id(),
        'id_ruang' => $request->id_ruang,
        'tanggal_pinjam' => $request->tanggal_pinjam,
        'tanggal_kembali' => $request->tanggal_kembali,
        'waktu_mulai' => $request->waktu_mulai,
        'waktu_selesai' => $request->waktu_selesai,
        'keperluan' => $request->keperluan,
        'status' => 'pending',
    ]);

    return redirect()->route('peminjam.peminjaman.index')
        ->with('success', 'Peminjaman berhasil diajukan');
}
```

---

#### FR-003: Approval System
**Requirement:** Admin/Petugas dapat approve atau reject peminjaman pending.

**Specification:**
- **Input**:
  - id_peminjaman (integer, required, exists)
  - action ('approve' | 'reject')
  - catatan (text, optional for approve, required for reject)

- **Business Rules**:
  1. Only pending status can be approved/rejected
  2. Rejection must have reason (catatan)
  3. Approval updates room status to 'dipakai'
  4. Send notification to peminjam

- **Process**:
  1. Validate peminjaman exists and status = pending
  2. If approve:
     - Update status to 'approved'
     - Update room status to 'dipakai'
  3. If reject:
     - Update status to 'rejected'
     - Save rejection reason (catatan)
  4. Send notification

- **Output**:
  - Success: Redirect with success message
  - Failure: Error message

**Implementation:** `app/Http/Controllers/Admin/PersetujuanController.php`

```php
public function approve($id)
{
    $peminjaman = Peminjaman::findOrFail($id);

    if ($peminjaman->status !== 'pending') {
        return back()->with('error', 'Peminjaman tidak dalam status pending');
    }

    $peminjaman->update(['status' => 'approved']);

    // Update room status
    $ruang = $peminjaman->ruang;
    $ruang->update(['status' => 'dipakai']);

    return redirect()->route('admin.persetujuan.index')
        ->with('success', 'Peminjaman berhasil disetujui');
}

public function reject(Request $request, $id)
{
    $request->validate([
        'catatan' => 'required|string|max:500',
    ]);

    $peminjaman = Peminjaman::findOrFail($id);

    if ($peminjaman->status !== 'pending') {
        return back()->with('error', 'Peminjaman tidak dalam status pending');
    }

    $peminjaman->update([
        'status' => 'rejected',
        'catatan' => $request->catatan,
    ]);

    return redirect()->route('admin.persetujuan.index')
        ->with('success', 'Peminjaman berhasil ditolak');
}
```

---

### 1.2 Non-Functional Requirements

#### NFR-001: Performance
- **Page Load Time**: < 2 seconds
- **Database Query**: Optimized dengan eager loading
- **Session Timeout**: 120 minutes
- **Max Concurrent Users**: 100
- **Implementation**:
  ```php
  // Eager loading untuk prevent N+1
  $ruangs = Ruang::with(['peminjamanAktif', 'penggunaDefault'])->get();
  
  // Database indexing
  $table->index('tanggal_pinjam');
  $table->index('status');
  ```

#### NFR-002: Security
- **Password Hashing**: bcrypt (cost factor 12)
- **CSRF Protection**: Laravel middleware
- **SQL Injection**: Query builder/Eloquent ORM
- **XSS Protection**: Blade auto-escaping
- **Session Security**: HTTP only cookies, secure flag (production)
- **Implementation**:
  ```php
  // Password hashing
  'password' => Hash::make($password)
  
  // CSRF token in forms
  @csrf
  
  // Blade escaping
  {{ $user->nama }} // Auto-escaped
  ```

#### NFR-003: Usability
- **Responsive Design**: Mobile-first approach
- **Touch Targets**: Minimum 44px × 44px
- **Error Messages**: Clear and actionable
- **Loading Indicators**: For async operations
- **Accessibility**: ARIA labels, semantic HTML

#### NFR-004: Scalability
- **Database**: Indexed columns for frequent queries
- **Caching**: Config/route caching (production)
- **Queue Jobs**: For heavy operations (email, reports)
- **CDN**: For static assets

#### NFR-005: Maintainability
- **Code Standards**: PSR-12
- **Version Control**: Git with semantic commits
- **Documentation**: Inline comments, README
- **Testing**: Unit tests for critical functions

---

## 2. Data Specification

### 2.1 Entity: User

**Table Name:** `users`

**Columns:**
```php
Schema::create('users', function (Blueprint $table) {
    $table->id('id_user');                    // Primary key
    $table->string('username', 50)->unique();  // Login username
    $table->string('password');                // Hashed with bcrypt
    $table->string('nama', 100);               // Full name
    $table->enum('role', ['admin', 'petugas', 'peminjam']); // User role
    $table->timestamps();                      // created_at, updated_at
});
```

**Constraints:**
- Username: Unique, max 50 characters
- Password: Min 8 characters (hashed)
- Role: Only 3 allowed values
- Nama: Required, max 100 characters

**Relationships:**
- `hasMany` Peminjaman (1 user → many peminjaman)

---

### 2.2 Entity: Ruang

**Table Name:** `ruang`

**Columns:**
```php
Schema::create('ruang', function (Blueprint $table) {
    $table->id('id_ruang');                          // Primary key
    $table->string('nama_ruang', 100);               // Room name
    $table->integer('kapasitas');                    // Capacity (number of people)
    $table->string('lokasi', 255)->nullable();       // Location description
    $table->enum('status', ['kosong', 'dipakai']);   // Availability status
    $table->string('pengguna_default', 100)->nullable(); // Default user/class
    $table->text('keterangan_penggunaan')->nullable();   // Usage notes
    $table->boolean('is_temporary_occupied')->default(false); // Temp relocation flag
    $table->string('pengguna_default_temp', 100)->nullable(); // Temp user
    $table->unsignedBigInteger('relocated_from')->nullable(); // FK to ruang
    $table->timestamps();
});
```

**Business Rules:**
- Kapasitas: Must be > 0
- Status: Auto-updates based on active bookings
- pengguna_default: Required if status = 'dipakai'
- is_temporary_occupied: Used for room relocation feature

**Relationships:**
- `hasMany` Peminjaman (1 ruang → many peminjaman)
- `belongsTo` User via pengguna_default (optional)

---

### 2.3 Entity: Peminjaman

**Table Name:** `peminjaman`

**Columns:**
```php
Schema::create('peminjaman', function (Blueprint $table) {
    $table->id('id_peminjaman');                  // Primary key
    $table->unsignedBigInteger('id_user');        // FK to users
    $table->unsignedBigInteger('id_ruang');       // FK to ruang
    $table->date('tanggal_pinjam');               // Start date
    $table->date('tanggal_kembali');              // End date
    $table->time('waktu_mulai');                  // Start time
    $table->time('waktu_selesai');                // End time
    $table->text('keperluan');                    // Purpose (max 500)
    $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled', 'selesai']);
    $table->text('catatan')->nullable();          // Admin notes
    $table->timestamps();
    
    $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
    $table->foreign('id_ruang')->references('id_ruang')->on('ruang')->onDelete('cascade');
    
    // Indexes for performance
    $table->index('tanggal_pinjam');
    $table->index('status');
});
```

**Business Rules:**
- tanggal_kembali >= tanggal_pinjam
- waktu_selesai > waktu_mulai (if same date)
- Status transitions (see Business Logic section)
- catatan: Required for rejection, optional for approval

**Relationships:**
- `belongsTo` User (peminjaman → 1 user)
- `belongsTo` Ruang (peminjaman → 1 ruang)

---

## 3. API Specification

### 3.1 Check Room Availability

**Endpoint:** `POST /api/ruang/check-availability`

**Description:** Check if a room is available on specific date and time range.

**Request Headers:**
```
Content-Type: application/json
Accept: application/json
X-CSRF-TOKEN: {token}
```

**Request Body:**
```json
{
    "id_ruang": 1,
    "tanggal": "2025-11-20",
    "waktu_mulai": "08:00",
    "waktu_selesai": "12:00"
}
```

**Success Response (Available):**
```json
{
    "available": true,
    "status": "kosong",
    "message": "Ruang tersedia untuk tanggal dan waktu tersebut"
}
```

**Success Response (Not Available):**
```json
{
    "available": false,
    "status": "dipakai",
    "message": "Ruang sedang dipakai",
    "current_booking": {
        "user": "John Doe",
        "tanggal_pinjam": "2025-11-20",
        "tanggal_kembali": "2025-11-20",
        "waktu_mulai": "08:00",
        "waktu_selesai": "16:00",
        "keperluan": "Rapat Koordinasi"
    }
}
```

**Error Response (Validation Failed):**
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "id_ruang": ["The id ruang field is required."],
        "tanggal": ["The tanggal must be a valid date."]
    }
}
```

**Implementation:** `app/Http/Controllers/Api/RuangController.php`

```php
public function checkAvailability(Request $request)
{
    $request->validate([
        'id_ruang' => 'required|exists:ruang,id_ruang',
        'tanggal' => 'required|date',
        'waktu_mulai' => 'nullable|date_format:H:i',
        'waktu_selesai' => 'nullable|date_format:H:i',
    ]);

    $ruang = Ruang::find($request->id_ruang);

    $query = Peminjaman::where('id_ruang', $request->id_ruang)
        ->where('status', 'approved')
        ->whereDate('tanggal_pinjam', '<=', $request->tanggal)
        ->whereDate('tanggal_kembali', '>=', $request->tanggal);

    if ($request->waktu_mulai && $request->waktu_selesai) {
        $query->where(function ($q) use ($request) {
            $q->whereBetween('waktu_mulai', [$request->waktu_mulai, $request->waktu_selesai])
              ->orWhereBetween('waktu_selesai', [$request->waktu_mulai, $request->waktu_selesai]);
        });
    }

    $booking = $query->with('user')->first();

    if ($booking) {
        return response()->json([
            'available' => false,
            'status' => 'dipakai',
            'message' => 'Ruang sedang dipakai',
            'current_booking' => [
                'user' => $booking->user->nama,
                'tanggal_pinjam' => $booking->tanggal_pinjam,
                'tanggal_kembali' => $booking->tanggal_kembali,
                'waktu_mulai' => $booking->waktu_mulai,
                'waktu_selesai' => $booking->waktu_selesai,
                'keperluan' => $booking->keperluan,
            ],
        ]);
    }

    return response()->json([
        'available' => true,
        'status' => 'kosong',
        'message' => 'Ruang tersedia untuk tanggal dan waktu tersebut',
    ]);
}
```

---

## 4. Business Logic Specification

### 4.1 Status Transition Rules

**Valid Transitions:**
```
pending → approved   (by admin/petugas)
pending → rejected   (by admin/petugas)
pending → cancelled  (by peminjam)
approved → selesai   (auto by system)
approved → cancelled (by admin only)
```

**Invalid Transitions:**
```
rejected → approved  ❌
rejected → pending   ❌
selesai → any status ❌
cancelled → any status ❌
approved → rejected  ❌
```

**State Diagram:**
```
     ┌──────────┐
     │ PENDING  │
     └────┬─────┘
          │
     ┌────┴─────┬──────────┬────────────┐
     │          │          │            │
     ▼          ▼          ▼            ▼
┌─────────┐ ┌─────────┐ ┌──────────┐ ┌─────────┐
│APPROVED │ │REJECTED │ │CANCELLED │ │  (end)  │
└────┬────┘ └─────────┘ └──────────┘ └─────────┘
     │
     ▼
┌─────────┐
│ SELESAI │
└─────────┘
```

---

### 4.2 Auto Status Update Logic

**Trigger:** Scheduled task every 5 minutes

**Algorithm:**
```
FOR EACH peminjaman WHERE status = 'approved'
    current_datetime = NOW()
    end_datetime = CONCAT(tanggal_kembali, ' ', waktu_selesai)
    
    IF current_datetime > end_datetime THEN
        peminjaman.status = 'selesai'
        
        // Check if no other approved bookings for this room
        has_other_bookings = COUNT(peminjaman 
            WHERE id_ruang = this.id_ruang 
            AND status = 'approved'
            AND current_datetime BETWEEN tanggal_pinjam AND tanggal_kembali
        ) > 0
        
        IF NOT has_other_bookings THEN
            ruang.status = 'kosong'
            
            // Handle temporary relocation
            IF ruang.is_temporary_occupied THEN
                CALL return_to_original_room(ruang)
            END IF
        END IF
    END IF
END FOR
```

**Implementation:** `app/Console/Commands/MarkFinishedPeminjaman.php`

```php
public function handle()
{
    $now = now();
    
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
        
        // Check room status
        $activeBookings = Peminjaman::where('id_ruang', $peminjaman->id_ruang)
            ->where('status', 'approved')
            ->where('tanggal_pinjam', '<=', $now->format('Y-m-d'))
            ->where('tanggal_kembali', '>=', $now->format('Y-m-d'))
            ->count();

        if ($activeBookings === 0) {
            $ruang = $peminjaman->ruang;
            $ruang->update(['status' => 'kosong']);
            
            if ($ruang->is_temporary_occupied) {
                // Handle relocation return
                app(RoomRelocationService::class)->returnToOriginalRoom($ruang);
            }
        }
    }

    $this->info("Marked {$finished->count()} peminjaman as finished");
}
```

---

## 5. Validation Specification

### 5.1 Login Validation

**File:** `app/Http/Requests/LoginRequest.php`

```php
public function rules(): array
{
    return [
        'username' => ['required', 'string', 'max:50'],
        'password' => ['required', 'string'],
    ];
}

public function messages(): array
{
    return [
        'username.required' => 'Username wajib diisi',
        'password.required' => 'Password wajib diisi',
    ];
}
```

---

### 5.2 Peminjaman Validation

**File:** `app/Http/Requests/StorePeminjamanRequest.php`

```php
public function rules(): array
{
    return [
        'id_ruang' => [
            'required',
            'exists:ruang,id_ruang',
        ],
        'tanggal_pinjam' => [
            'required',
            'date',
            'after_or_equal:today',
        ],
        'tanggal_kembali' => [
            'required',
            'date',
            'after_or_equal:tanggal_pinjam',
        ],
        'waktu_mulai' => [
            'required',
            'date_format:H:i',
        ],
        'waktu_selesai' => [
            'required',
            'date_format:H:i',
            'after:waktu_mulai', // Only works if same date
        ],
        'keperluan' => [
            'required',
            'string',
            'max:500',
        ],
    ];
}

public function messages(): array
{
    return [
        'id_ruang.required' => 'Ruangan harus dipilih',
        'id_ruang.exists' => 'Ruangan tidak ditemukan',
        'tanggal_pinjam.after_or_equal' => 'Tanggal pinjam tidak boleh lampau',
        'tanggal_kembali.after_or_equal' => 'Tanggal kembali harus >= tanggal pinjam',
        'waktu_selesai.after' => 'Waktu selesai harus lebih dari waktu mulai',
        'keperluan.max' => 'Keperluan maksimal 500 karakter',
    ];
}

public function withValidator($validator)
{
    $validator->after(function ($validator) {
        // Custom validation: check room availability
        if (!$this->checkRoomAvailability()) {
            $validator->errors()->add(
                'id_ruang', 
                'Ruangan tidak tersedia pada tanggal dan waktu tersebut'
            );
        }
    });
}

private function checkRoomAvailability(): bool
{
    $conflict = Peminjaman::where('id_ruang', $this->id_ruang)
        ->where('status', 'approved')
        ->where(function ($query) {
            $query->whereBetween('tanggal_pinjam', [$this->tanggal_pinjam, $this->tanggal_kembali])
                ->orWhereBetween('tanggal_kembali', [$this->tanggal_pinjam, $this->tanggal_kembali])
                ->orWhere(function ($q) {
                    $q->where('tanggal_pinjam', '<=', $this->tanggal_pinjam)
                      ->where('tanggal_kembali', '>=', $this->tanggal_kembali);
                });
        })
        ->exists();

    return !$conflict;
}
```

---

## 🎯 Latihan Soal

### Soal 1: Requirement Analysis
**Q:** Apa perbedaan antara Functional dan Non-Functional Requirements? Berikan contoh dari project ini.

<details>
<summary>Jawaban</summary>

**Functional Requirements:**
- **Definisi**: Apa yang sistem HARUS BISA LAKUKAN (features)
- **Contoh**: 
  - User bisa login
  - Peminjam bisa ajukan booking
  - Admin bisa approve/reject

**Non-Functional Requirements:**
- **Definisi**: BAGAIMANA sistem bekerja (quality attributes)
- **Contoh**:
  - Performance: Page load < 2 detik
  - Security: Password hashing dengan bcrypt
  - Usability: Responsive design untuk mobile
</details>

### Soal 2: Status Transition
**Q:** Apakah peminjaman dengan status 'rejected' bisa diubah menjadi 'approved'? Mengapa?

<details>
<summary>Jawaban</summary>

**Tidak bisa.**

**Alasan:**
1. Business logic: Status rejected adalah final state
2. Data integrity: Rejection reason (catatan) sudah ada, tidak konsisten jika di-approve
3. Audit trail: Perubahan status harus traceable

**Solusi jika peminjam ingin booking lagi:**
- Peminjam buat peminjaman baru dengan id_peminjaman berbeda
- Data rejection tetap tersimpan untuk audit
</details>

### Soal 3: Validation Strategy
**Q:** Mengapa validation dilakukan di Form Request bukan di Controller?

<details>
<summary>Jawaban</summary>

**Keuntungan Form Request:**
1. **Separation of Concerns**: Controller fokus ke business logic
2. **Reusability**: Validation rules bisa dipakai di multiple controllers
3. **Cleaner Code**: Controller lebih readable
4. **Automatic Error Handling**: Laravel auto-redirect dengan errors
5. **Type Hinting**: Better IDE support dan type safety

**Example:**
```php
// ✅ GOOD - Clean controller
public function store(StorePeminjamanRequest $request) {
    // $request sudah validated
    Peminjaman::create($request->validated());
}

// ❌ BAD - Messy controller
public function store(Request $request) {
    // 50 lines of validation rules
    $request->validate([...]);
    // Business logic
}
```
</details>

---

## ✅ Checklist Pemahaman

- [ ] Bisa jelaskan functional requirements dari setiap fitur
- [ ] Bisa jelaskan non-functional requirements (performance, security, usability)
- [ ] Bisa menggambar ERD (Entity Relationship Diagram)
- [ ] Bisa menjelaskan status transition rules
- [ ] Bisa implementasi custom validation
- [ ] Bisa menjelaskan API specification (request/response format)
- [ ] Bisa menjelaskan business logic untuk auto-update status

---

## 📚 Referensi

1. **Requirements Engineering**: https://www.ibm.com/topics/requirements-engineering
2. **Laravel Validation**: https://laravel.com/docs/11.x/validation
3. **API Design Best Practices**: https://restfulapi.net/
4. **Database Design**: https://www.lucidchart.com/pages/database-diagram/database-design

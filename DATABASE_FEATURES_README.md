# 📊 PANDUAN DATABASE FEATURES - SISTEM PEMINJAMAN RUANG

Dokumentasi lengkap untuk menggunakan **Function, Stored Procedure, Trigger, Transaction (Commit & Rollback)** di sistem peminjaman ruang.

---

## 📋 DAFTAR ISI

1. [Functions](#1-functions)
2. [Stored Procedures](#2-stored-procedures)
3. [Triggers](#3-triggers)
4. [Transactions (Commit & Rollback)](#4-transactions-commit--rollback)
5. [Testing](#5-testing)
6. [Troubleshooting](#6-troubleshooting)

---

## **1. FUNCTIONS**

Functions adalah fungsi yang mengembalikan nilai tunggal dan dapat digunakan dalam query SQL.

### **A. count_peminjaman_by_user**

**Deskripsi:** Menghitung total peminjaman berdasarkan user ID.

**Parameter:**
- `user_id` (INT) - ID user yang ingin dihitung peminjamannya

**Return:** INT - Jumlah total peminjaman

**Cara Pakai di Laravel:**

```php
// Via Controller atau Tinker
$userId = 12;
$total = DB::select('SELECT count_peminjaman_by_user(?) AS total', [$userId])[0]->total;
echo "Total peminjaman user $userId: $total";
```

**Cara Pakai di phpMyAdmin:**

```sql
-- Hitung peminjaman user ID 12
SELECT count_peminjaman_by_user(12) AS total_peminjaman;
```

**Implementasi di Controller:**

```php
// File: app/Http/Controllers/Admin/DashboardController.php
public function index()
{
    $userId = Auth::id();
    $userPeminjamanCount = DB::select(
        'SELECT count_peminjaman_by_user(?) AS total', 
        [$userId]
    )[0]->total;
    
    return view('admin.dashboard', compact('userPeminjamanCount'));
}
```

---

### **B. count_peminjaman_by_status**

**Deskripsi:** Menghitung total peminjaman berdasarkan status.

**Parameter:**
- `p_status` (VARCHAR) - Status peminjaman: 'pending', 'approved', 'rejected', 'selesai'

**Return:** INT - Jumlah peminjaman dengan status tersebut

**Cara Pakai di Laravel:**

```php
// Di Controller
$pendingCount = DB::select('SELECT count_peminjaman_by_status(?) AS total', ['pending'])[0]->total;
$approvedCount = DB::select('SELECT count_peminjaman_by_status(?) AS total', ['approved'])[0]->total;
$rejectedCount = DB::select('SELECT count_peminjaman_by_status(?) AS total', ['rejected'])[0]->total;
$selesaiCount = DB::select('SELECT count_peminjaman_by_status(?) AS total', ['selesai'])[0]->total;

return view('admin.laporan.index', compact(
    'pendingCount', 
    'approvedCount', 
    'rejectedCount', 
    'selesaiCount'
));
```

**Cara Pakai di phpMyAdmin:**

```sql
-- Hitung semua status
SELECT 
    count_peminjaman_by_status('pending') AS pending,
    count_peminjaman_by_status('approved') AS approved,
    count_peminjaman_by_status('rejected') AS rejected,
    count_peminjaman_by_status('selesai') AS selesai;
```

**Implementasi Real:**

```php
// File: app/Http/Controllers/Admin/LaporanController.php
public function index()
{
    $stats = [
        'pending' => DB::select('SELECT count_peminjaman_by_status(?) AS total', ['pending'])[0]->total,
        'approved' => DB::select('SELECT count_peminjaman_by_status(?) AS total', ['approved'])[0]->total,
        'rejected' => DB::select('SELECT count_peminjaman_by_status(?) AS total', ['rejected'])[0]->total,
        'selesai' => DB::select('SELECT count_peminjaman_by_status(?) AS total', ['selesai'])[0]->total,
    ];
    
    return view('admin.laporan.index', compact('stats'));
}
```

---

### **C. check_room_availability**

**Deskripsi:** Cek ketersediaan ruangan pada tanggal dan waktu tertentu.

**Parameter:**
- `p_ruang_id` (INT) - ID ruangan
- `p_tanggal` (DATE) - Tanggal peminjaman (format: YYYY-MM-DD)
- `p_waktu_mulai` (TIME) - Waktu mulai (format: HH:MM:SS)
- `p_waktu_selesai` (TIME) - Waktu selesai (format: HH:MM:SS)

**Return:** VARCHAR - 'TERSEDIA' atau 'TIDAK TERSEDIA'

**Cara Pakai di Laravel:**

```php
// AJAX Check Availability
public function checkAvailability(Request $request)
{
    $availability = DB::select(
        'SELECT check_room_availability(?, ?, ?, ?) AS status',
        [
            $request->ruang_id,
            $request->tanggal,
            $request->waktu_mulai,
            $request->waktu_selesai
        ]
    )[0]->status;
    
    return response()->json([
        'available' => $availability === 'TERSEDIA',
        'message' => $availability
    ]);
}
```

**Cara Pakai di phpMyAdmin:**

```sql
-- Cek Lab Komputer (ID=1) pada 25 Nov 2024, jam 08:00-10:00
SELECT check_room_availability(1, '2024-11-25', '08:00:00', '10:00:00') AS status;
```

**Implementasi AJAX di Frontend:**

```javascript
// File: resources/views/peminjam/peminjaman/create.blade.php
$('#ruang_id, #tanggal_pinjam, #waktu_mulai, #waktu_selesai').on('change', function() {
    let ruangId = $('#ruang_id').val();
    let tanggal = $('#tanggal_pinjam').val();
    let waktuMulai = $('#waktu_mulai').val();
    let waktuSelesai = $('#waktu_selesai').val();
    
    if (ruangId && tanggal && waktuMulai && waktuSelesai) {
        $.post('/check-availability', {
            ruang_id: ruangId,
            tanggal: tanggal,
            waktu_mulai: waktuMulai,
            waktu_selesai: waktuSelesai,
            _token: $('meta[name="csrf-token"]').attr('content')
        }, function(response) {
            if (response.available) {
                $('#availability-message').html('<span class="text-success">✓ Ruangan tersedia</span>');
            } else {
                $('#availability-message').html('<span class="text-danger">✗ Ruangan tidak tersedia</span>');
            }
        });
    }
});
```

---

## **2. STORED PROCEDURES**

Stored Procedures adalah kumpulan statement SQL yang disimpan di database dan dapat dipanggil berkali-kali.

### **A. approve_peminjaman**

**Deskripsi:** Menyetujui peminjaman dengan transaction (atomic operation).

**Parameter:**
- `p_peminjaman_id` (INT) - ID peminjaman yang akan disetujui
- `p_catatan` (TEXT) - Catatan approval
- `p_approved_by` (INT) - ID user yang menyetujui (admin/petugas)

**Proses yang Dilakukan:**
1. ✅ Update status peminjaman menjadi 'approved'
2. ✅ Update status ruangan menjadi 'dipakai'
3. ✅ Create notification untuk user peminjam
4. ✅ Semua dalam 1 transaction (all or nothing)

**Cara Pakai di Laravel:**

```php
// File: app/Http/Controllers/Admin/PersetujuanController.php
public function approve(Request $request, $id)
{
    try {
        // Call stored procedure
        DB::statement('CALL approve_peminjaman(?, ?, ?)', [
            $id,                                    // peminjaman_id
            $request->catatan ?? 'Disetujui',      // catatan
            Auth::id()                              // approved_by
        ]);

        return redirect()->route('admin.persetujuan.index')
            ->with('success', 'Peminjaman berhasil disetujui');

    } catch (\Exception $e) {
        return redirect()->back()
            ->withErrors(['error' => 'Gagal: ' . $e->getMessage()]);
    }
}
```

**Cara Pakai di phpMyAdmin:**

```sql
-- Approve peminjaman ID 13, oleh admin ID 1
CALL approve_peminjaman(13, 'Peminjaman disetujui untuk praktikum', 1);

-- Cek hasil
SELECT * FROM peminjaman WHERE id_peminjaman = 13;
SELECT * FROM notifications WHERE user_id = (SELECT id_user FROM peminjaman WHERE id_peminjaman = 13);
```

**Frontend Implementation:**

```blade
<!-- File: resources/views/admin/persetujuan/index.blade.php -->
<form action="{{ route('admin.persetujuan.approve', $peminjaman->id_peminjaman) }}" 
      method="POST"
      onsubmit="return confirm('Yakin menyetujui peminjaman ini?')">
    @csrf
    
    <textarea name="catatan" class="form-control" placeholder="Catatan (opsional)"></textarea>
    
    <button type="submit" class="btn btn-success">
        <i class="fas fa-check"></i> Setujui
    </button>
</form>
```

---

### **B. reject_peminjaman**

**Deskripsi:** Menolak peminjaman dengan alasan.

**Parameter:**
- `p_peminjaman_id` (INT) - ID peminjaman yang ditolak
- `p_catatan` (TEXT) - Alasan penolakan (REQUIRED)
- `p_rejected_by` (INT) - ID user yang menolak

**Proses yang Dilakukan:**
1. ✅ Update status peminjaman menjadi 'rejected'
2. ✅ Create notification untuk user dengan alasan penolakan
3. ✅ Dalam transaction

**Cara Pakai di Laravel:**

```php
public function reject(Request $request, $id)
{
    $request->validate([
        'catatan' => 'required|string|max:500'
    ]);

    try {
        DB::statement('CALL reject_peminjaman(?, ?, ?)', [
            $id,
            $request->catatan,
            Auth::id()
        ]);

        return redirect()->route('admin.persetujuan.index')
            ->with('success', 'Peminjaman berhasil ditolak');

    } catch (\Exception $e) {
        return redirect()->back()
            ->withErrors(['error' => $e->getMessage()]);
    }
}
```

**Cara Pakai di phpMyAdmin:**

```sql
-- Tolak peminjaman ID 14
CALL reject_peminjaman(14, 'Ruangan sudah dibooking untuk acara kampus', 1);

-- Cek hasil
SELECT status, catatan FROM peminjaman WHERE id_peminjaman = 14;
```

---

## **3. TRIGGERS**

Triggers adalah event yang otomatis dijalankan ketika ada INSERT, UPDATE, atau DELETE pada tabel.

### **A. log_peminjaman_status_change**

**Event:** AFTER UPDATE ON peminjaman

**Deskripsi:** Otomatis mencatat perubahan status peminjaman ke tabel activity_log.

**Kapan Dijalankan:** Setiap kali status peminjaman berubah (pending → approved, approved → selesai, dll)

**Tidak Perlu Dipanggil Manual!** Trigger berjalan otomatis.

**Contoh:**

```php
// Di Controller
$peminjaman = Peminjaman::find(15);
$peminjaman->status = 'approved';
$peminjaman->save();

// ↑ Trigger otomatis jalan dan insert ke activity_log!
```

**Lihat Hasil di Activity Log:**

```php
// Via Tinker
$logs = DB::table('activity_log')
    ->where('peminjaman_id', 15)
    ->orderBy('created_at', 'desc')
    ->get();

foreach ($logs as $log) {
    echo "{$log->created_at}: {$log->old_status} → {$log->new_status}\n";
}
```

**Via phpMyAdmin:**

```sql
-- Lihat semua log perubahan status
SELECT * FROM activity_log 
WHERE action = 'status_change' 
ORDER BY created_at DESC 
LIMIT 20;
```

**View Activity Log:**

```
http://localhost:8000/admin/activity-log
```

---

### **B. log_peminjaman_created**

**Event:** AFTER INSERT ON peminjaman

**Deskripsi:** Otomatis log saat peminjaman baru dibuat.

**Contoh:**

```php
// Buat peminjaman baru
Peminjaman::create([
    'id_user' => Auth::id(),
    'id_ruang' => 1,
    'tanggal_pinjam' => '2024-11-25',
    'waktu_mulai' => '08:00:00',
    // ... data lainnya
]);

// ↑ Trigger otomatis log ke activity_log dengan action='created'
```

---

### **C. decrease_room_slots**

**Event:** AFTER UPDATE ON peminjaman

**Kondisi:** Status berubah dari bukan 'approved' menjadi 'approved'

**Deskripsi:** Otomatis mengurangi available_slots ruangan saat peminjaman diapprove.

**Contoh:**

```php
// Approve peminjaman
$peminjaman = Peminjaman::find(16);
$peminjaman->status = 'approved';  // OLD: 'pending' → NEW: 'approved'
$peminjaman->save();

// ↑ Trigger otomatis:
// UPDATE ruang SET available_slots = available_slots - 1
```

**Lihat Perubahan:**

```sql
-- Sebelum approve: available_slots = 5
-- Setelah approve: available_slots = 4 (berkurang 1 otomatis)
SELECT id_ruang, nama_ruang, available_slots FROM ruang WHERE id_ruang = 1;
```

---

### **D. increase_room_slots**

**Event:** AFTER UPDATE ON peminjaman

**Kondisi:** Status berubah dari 'approved' ke 'rejected'/'cancelled'/'selesai'

**Deskripsi:** Otomatis mengembalikan available_slots saat peminjaman dibatalkan/selesai.

**Contoh:**

```php
// Selesaikan peminjaman
$peminjaman = Peminjaman::find(16);
$peminjaman->status = 'selesai';  // OLD: 'approved' → NEW: 'selesai'
$peminjaman->save();

// ↑ Trigger otomatis:
// UPDATE ruang SET available_slots = available_slots + 1
```

---

## **4. TRANSACTIONS (COMMIT & ROLLBACK)**

Transaction memastikan semua operasi database berhasil atau tidak sama sekali (atomicity).

### **A. Manual Transaction dengan DB::transaction()**

**Contoh Sukses (COMMIT):**

```php
use Illuminate\Support\Facades\DB;

DB::transaction(function () use ($request, $id) {
    // Step 1: Update peminjaman
    $peminjaman = Peminjaman::findOrFail($id);
    $peminjaman->status = 'approved';
    $peminjaman->save();
    
    // Step 2: Update room
    $peminjaman->ruang->status = 'dipakai';
    $peminjaman->ruang->save();
    
    // Step 3: Create notification
    Notification::create([
        'user_id' => $peminjaman->id_user,
        'title' => 'Peminjaman Disetujui',
        'message' => 'Peminjaman berhasil disetujui',
        'type' => 'approval'
    ]);
    
    // Jika semua berhasil: AUTO COMMIT ✓
});

// Jika ada error: AUTO ROLLBACK ✗
```

---

### **B. Manual Transaction dengan BEGIN/COMMIT/ROLLBACK**

**Contoh dengan Error Handling:**

```php
DB::beginTransaction();

try {
    // Step 1: Update peminjaman
    $peminjaman = Peminjaman::findOrFail($id);
    $peminjaman->status = 'approved';
    $peminjaman->catatan = $request->catatan;
    $peminjaman->save();
    
    // Step 2: Update room
    $ruang = Ruang::findOrFail($peminjaman->id_ruang);
    $ruang->status = 'dipakai';
    $ruang->save();
    
    // Step 3: Create notification
    Notification::create([
        'user_id' => $peminjaman->id_user,
        'title' => 'Peminjaman Disetujui',
        'message' => "Peminjaman ruangan {$ruang->nama_ruang} disetujui",
        'type' => 'approval'
    ]);
    
    // Step 4: Log activity
    ActivityLog::create([
        'action' => 'approve',
        'user_id' => Auth::id(),
        'peminjaman_id' => $id,
        'description' => 'Peminjaman approved'
    ]);
    
    // COMMIT: Semua berhasil!
    DB::commit();
    
    return redirect()->back()->with('success', 'Peminjaman berhasil disetujui');
    
} catch (\Exception $e) {
    // ROLLBACK: Ada error, batalkan semua!
    DB::rollBack();
    
    \Log::error('Approve failed: ' . $e->getMessage());
    
    return redirect()->back()
        ->withErrors(['error' => 'Gagal: ' . $e->getMessage()]);
}
```

---

### **C. Transaction Scenario: SUCCESS vs ROLLBACK**

**Scenario SUCCESS:**

```php
DB::beginTransaction();

// Step 1: Update peminjaman ✓
$peminjaman->status = 'approved';
$peminjaman->save();

// Step 2: Update room ✓
$ruang->status = 'dipakai';
$ruang->save();

// Step 3: Create notification ✓
Notification::create([...]);

// All success → COMMIT
DB::commit();

// Result: Semua data tersimpan permanent di database
```

**Scenario ROLLBACK:**

```php
DB::beginTransaction();

// Step 1: Update peminjaman ✓ (temporary)
$peminjaman->status = 'approved';
$peminjaman->save();

// Step 2: Update room ✓ (temporary)
$ruang->status = 'dipakai';
$ruang->save();

// Step 3: Create notification ✗ (ERROR!)
// Database connection lost!
throw new \Exception("Connection error");

// Error detected → ROLLBACK
DB::rollBack();

// Result: Semua perubahan dibatalkan, data kembali seperti semula
// - peminjaman status: masih 'pending'
// - ruang status: masih 'kosong'
// - notification: tidak dibuat
```

---

### **D. Nested Transactions dengan SAVEPOINT**

```php
DB::beginTransaction();

try {
    // Update peminjaman 1
    $p1 = Peminjaman::find(20);
    $p1->status = 'approved';
    $p1->save();
    
    DB::statement('SAVEPOINT save1');
    
    // Update peminjaman 2
    $p2 = Peminjaman::find(21);
    $p2->status = 'approved';
    $p2->save();
    
    DB::statement('SAVEPOINT save2');
    
    // Update peminjaman 3 (error!)
    $p3 = Peminjaman::find(999); // ID tidak ada!
    $p3->status = 'approved';
    $p3->save();
    
} catch (\Exception $e) {
    // Rollback hanya ke savepoint terakhir
    DB::statement('ROLLBACK TO SAVEPOINT save2');
    
    // Peminjaman 1 & 2 tetap approved
    // Peminjaman 3 gagal tapi tidak affect 1 & 2
}

DB::commit();
```

---

## **5. TESTING**

### **A. Test Functions via Tinker**

```bash
php artisan tinker
```

```php
// Test count_peminjaman_by_status
DB::select('SELECT count_peminjaman_by_status(?) AS total', ['pending'])[0]->total;

// Test check_room_availability
DB::select('SELECT check_room_availability(?, ?, ?, ?) AS status', 
    [1, '2024-11-25', '08:00:00', '10:00:00'])[0]->status;

// Test count_peminjaman_by_user
DB::select('SELECT count_peminjaman_by_user(?) AS total', [12])[0]->total;
```

---

### **B. Test Stored Procedures via Tinker**

```php
// Test approve_peminjaman
DB::statement('CALL approve_peminjaman(?, ?, ?)', [13, 'Test approval', 1]);

// Cek hasil
$peminjaman = DB::table('peminjaman')->where('id_peminjaman', 13)->first();
echo "Status: " . $peminjaman->status; // Should be 'approved'

// Cek notification
$notif = DB::table('notifications')
    ->where('user_id', $peminjaman->id_user)
    ->latest()
    ->first();
echo "Notif: " . $notif->message;
```

---

### **C. Test Triggers via Browser**

1. **Login sebagai Admin**
2. **Buka:** `http://localhost:8000/admin/persetujuan`
3. **Approve peminjaman** dengan klik tombol "Setujui"
4. **Buka:** `http://localhost:8000/admin/activity-log`
5. **Lihat log baru** yang tercatat otomatis oleh trigger

---

### **D. Test Transaction ROLLBACK**

```php
// Via Tinker
DB::beginTransaction();

try {
    $p = \App\Models\Peminjaman::find(15);
    $p->status = 'approved';
    $p->save();
    
    // Simulate error
    throw new \Exception("Test rollback");
    
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    echo "ROLLBACK: " . $e->getMessage();
}

// Check data
$p = \App\Models\Peminjaman::find(15);
echo "Status: " . $p->status; // Should still be 'pending'
```

---

## **6. TROUBLESHOOTING**

### **Error: Function does not exist**

```bash
# Cek apakah function ada
php artisan tinker
>>> DB::select('SHOW FUNCTION STATUS WHERE Db = ?', ['peminjaman_ruang']);

# Jika kosong, buat manual di phpMyAdmin (lihat DATABASE_FEATURES_README.md)
```

---

### **Error: Procedure does not exist**

```bash
# Cek procedures
>>> DB::select('SHOW PROCEDURE STATUS WHERE Db = ?', ['peminjaman_ruang']);

# Jika kosong, buat manual di phpMyAdmin
```

---

### **Error: Trigger not firing**

```sql
-- Cek trigger di phpMyAdmin
SHOW TRIGGERS FROM peminjaman_ruang;

-- Test manual
UPDATE peminjaman SET status = 'approved' WHERE id_peminjaman = 10;

-- Cek activity_log
SELECT * FROM activity_log WHERE peminjaman_id = 10 ORDER BY created_at DESC;
```

---

### **Error: Transaction ROLLBACK tidak bekerja**

```php
// Pastikan menggunakan InnoDB (bukan MyISAM)
DB::select('SHOW TABLE STATUS WHERE Name = "peminjaman"');

// Jika Engine = MyISAM, ubah ke InnoDB:
DB::statement('ALTER TABLE peminjaman ENGINE = InnoDB');
DB::statement('ALTER TABLE ruang ENGINE = InnoDB');
DB::statement('ALTER TABLE notifications ENGINE = InnoDB');
```

---

### **Clear Cache Jika Ada Masalah**

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
```

---

## **7. REFERENSI LENGKAP**

### **Route List:**

```bash
php artisan route:list | grep -E "(persetujuan|activity)"
```

### **Database Schema:**

```bash
php artisan tinker
>>> DB::select('DESCRIBE peminjaman');
>>> DB::select('DESCRIBE activity_log');
>>> DB::select('DESCRIBE notifications');
```

### **All Stored Objects:**

```sql
-- Functions
SELECT ROUTINE_NAME, ROUTINE_TYPE 
FROM information_schema.ROUTINES 
WHERE ROUTINE_SCHEMA = 'peminjaman_ruang' 
AND ROUTINE_TYPE = 'FUNCTION';

-- Procedures
SELECT ROUTINE_NAME, ROUTINE_TYPE 
FROM information_schema.ROUTINES 
WHERE ROUTINE_SCHEMA = 'peminjaman_ruang' 
AND ROUTINE_TYPE = 'PROCEDURE';

-- Triggers
SELECT TRIGGER_NAME, EVENT_MANIPULATION, EVENT_OBJECT_TABLE 
FROM information_schema.TRIGGERS 
WHERE TRIGGER_SCHEMA = 'peminjaman_ruang';
```

---

## **📞 SUPPORT**

Jika ada pertanyaan atau issue:
1. Cek log: `storage/logs/laravel.log`
2. Cek MySQL error log: `/opt/lampp/logs/mysql_error.log` (XAMPP)
3. Enable query log untuk debugging:
   ```php
   DB::enableQueryLog();
   // ... your code ...
   dd(DB::getQueryLog());
   ```

---

**Created:** November 20, 2025  
**Last Updated:** November 20, 2025  
**Version:** 1.0

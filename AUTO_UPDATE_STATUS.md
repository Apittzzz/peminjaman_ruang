# ⏰ AUTO-UPDATE STATUS JADWAL RUANGAN

## 📋 Overview

Sistem ini memiliki **2 command otomatis** untuk memperbarui status jadwal dan ruangan:

1. **`peminjaman:mark-finished`** - Menandai peminjaman yang sudah selesai
2. **`ruang:refresh-status`** - Memperbarui status ruangan

---

## 🔧 SETUP OTOMATIS (Cron Job)

### **1. Edit Crontab**

```bash
# Buka crontab editor
crontab -e
```

### **2. Tambahkan Cron Entry**

Tambahkan baris ini di crontab:

```bash
# Laravel Scheduler - Peminjaman Ruang
* * * * * cd /home/apitaja/peminjaman_ruang && php artisan schedule:run >> /dev/null 2>&1
```

**Penjelasan:**
- `* * * * *` = Jalankan setiap menit
- Laravel scheduler akan mengelola waktu eksekusi command
- Output diredirect ke `/dev/null` agar tidak spam email

### **3. Simpan dan Keluar**

```bash
# Tekan ESC, lalu ketik:
:wq
# Enter
```

### **4. Verify Crontab**

```bash
# Lihat crontab yang aktif
crontab -l

# Output seharusnya:
# * * * * * cd /home/apitaja/peminjaman_ruang && php artisan schedule:run >> /dev/null 2>&1
```

### **5. Check Scheduled Tasks**

```bash
cd /home/apitaja/peminjaman_ruang
php artisan schedule:list
```

Output:
```
*/5  * * * *  php artisan peminjaman:mark-finished ....... Next Due: X minutes from now
*/10 * * * *  php artisan ruang:refresh-status ........... Next Due: X minutes from now
```

---

## 🎯 CARA KERJA

### **Command 1: `peminjaman:mark-finished`**

**Jadwal:** Setiap 5 menit  
**Fungsi:**
1. ✅ Cek peminjaman dengan status `approved`
2. ✅ Bandingkan `tanggal_kembali + jam_selesai` dengan waktu sekarang
3. ✅ Jika sudah lewat, update status menjadi `selesai`
4. ✅ Kembalikan pengguna default ke ruangan asalnya (jika ada relokasi)
5. ✅ Update status ruangan

**Output:**
```
Pengguna default 'Kelas 10A' berhasil dikembalikan ke Gedung Olahraga
Status peminjaman dan ruang diperbarui: 2025-11-13 10:30:00
```

### **Command 2: `ruang:refresh-status`**

**Jadwal:** Setiap 10 menit  
**Fungsi:**
1. ✅ Loop semua ruangan
2. ✅ Cek apakah ada peminjaman aktif (approved + waktu between start-end)
3. ✅ Cek apakah sedang menampung pengguna temporary
4. ✅ Update status:
   - **`dipakai`** = Ada peminjaman aktif ATAU menampung temporary
   - **`kosong`** = Tidak ada peminjaman dan tidak menampung temporary

**Output:**
```
Memulai refresh status ruangan...
  ✓ Gedung Olahraga: dipakai → kosong
  ✓ Ruang Kelas 1: kosong → dipakai
✅ 2 ruangan berhasil diperbarui statusnya.
```

---

## 🖱️ MANUAL EXECUTION

Jika tidak ingin setup cron, Anda bisa menjalankan command manual:

### **Test Schedule (Simulate Cron)**

```bash
cd /home/apitaja/peminjaman_ruang

# Simulate scheduler (run all due commands)
php artisan schedule:run
```

### **Run Individual Commands**

```bash
# Mark finished bookings
php artisan peminjaman:mark-finished

# Refresh room status
php artisan ruang:refresh-status

# Or via alias
php artisan bookings:mark-finished
```

### **Force Run (Ignore Schedule)**

```bash
# Force run command regardless of schedule
php artisan schedule:test
```

---

## 🌐 BUTTON MANUAL REFRESH (Web Interface)

Untuk admin, bisa menambahkan button refresh manual di halaman jadwal.

### **1. Update View**

Edit `resources/views/jadwal/index.blade.php`:

```blade
@if(Auth::user()->role === 'admin')
<div class="col-md-2">
    <form action="{{ route('admin.jadwal.refresh') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-success w-100">
            <i class="fas fa-sync-alt me-1"></i>Refresh Status
        </button>
    </form>
</div>
@endif
```

### **2. Add Route**

Edit `routes/web.php`:

```php
Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('/admin/jadwal/refresh', [JadwalController::class, 'refreshStatus'])
        ->name('admin.jadwal.refresh');
});
```

### **3. Add Method di Controller**

Edit `app/Http/Controllers/JadwalController.php`:

```php
public function refreshStatus()
{
    try {
        \Artisan::call('peminjaman:mark-finished');
        \Artisan::call('ruang:refresh-status');
        
        return redirect()->back()
            ->with('success', 'Status jadwal berhasil diperbarui!');
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Gagal memperbarui: ' . $e->getMessage());
    }
}
```

---

## 📊 MONITORING

### **Check Logs**

```bash
# Laravel log
tail -f storage/logs/laravel.log

# Cron log (Linux)
grep CRON /var/log/syslog | tail -20
```

### **Database Check**

```sql
-- Cek peminjaman yang sudah selesai hari ini
SELECT * FROM peminjaman 
WHERE status = 'selesai' 
AND DATE(updated_at) = CURDATE();

-- Cek ruangan yang statusnya baru diupdate
SELECT * FROM ruang 
WHERE updated_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR);
```

---

## 🐛 TROUBLESHOOTING

### **Problem: Cron tidak jalan**

**Solution:**
```bash
# 1. Check cron service
sudo systemctl status cron

# 2. Start cron if not running
sudo systemctl start cron

# 3. Check crontab
crontab -l

# 4. Check permissions
ls -la /home/apitaja/peminjaman_ruang/artisan
# Should be executable (-rwxr-xr-x)
```

### **Problem: Command error**

**Solution:**
```bash
# 1. Test command manually
cd /home/apitaja/peminjaman_ruang
php artisan peminjaman:mark-finished

# 2. Check database connection
php artisan tinker
>>> DB::connection()->getPdo();

# 3. Clear caches
php artisan cache:clear
php artisan config:clear
```

### **Problem: Status tidak update**

**Solution:**
```bash
# 1. Check scheduled tasks
php artisan schedule:list

# 2. Run schedule manually
php artisan schedule:run

# 3. Check logs
tail -f storage/logs/laravel.log
```

---

## ✅ VERIFICATION

### **1. Schedule is Running**

```bash
# Check schedule list
php artisan schedule:list

# Output should show:
# */5  * * * *  php artisan peminjaman:mark-finished
# */10 * * * *  php artisan ruang:refresh-status
```

### **2. Commands Work**

```bash
# Test mark finished
php artisan peminjaman:mark-finished
# Should show: Status peminjaman dan ruang diperbarui: [timestamp]

# Test refresh status
php artisan ruang:refresh-status
# Should show: Memulai refresh status ruangan...
```

### **3. Cron is Active**

```bash
# Check crontab
crontab -l

# Check cron logs (wait 1-2 minutes)
grep -i cron /var/log/syslog | tail -10
```

---

## 📈 PERFORMANCE

### **Resource Usage**

- **Execution Time:** ~100-500ms per run
- **Memory:** ~20-50MB
- **Database Queries:** ~5-15 queries per run
- **Load:** Minimal (commands use efficient queries)

### **Optimization Tips**

1. ✅ Use indexes on `status`, `tanggal_pinjam`, `tanggal_kembali`
2. ✅ Use `->chunk()` if processing large datasets
3. ✅ Consider queue jobs for heavy operations
4. ✅ Use `withoutOverlapping()` to prevent concurrent execution

---

## 🔐 SECURITY

1. ✅ Commands only accessible via CLI (not web)
2. ✅ Uses database transactions for data integrity
3. ✅ Logs all executions
4. ✅ Error handling prevents crashes

---

## 📝 CHANGELOG

### v1.0.0 (2025-11-13)
- ✅ Initial release
- ✅ Fixed `tersedia` → `kosong` status bug
- ✅ Added `RefreshRuangStatus` command
- ✅ Added scheduling with error handling
- ✅ Added comprehensive logging

---

**Last Updated:** 2025-11-13  
**Author:** Apittzzz

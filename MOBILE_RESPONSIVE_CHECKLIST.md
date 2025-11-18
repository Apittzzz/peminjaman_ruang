# 📱 MOBILE RESPONSIVE CHECKLIST

## ✅ Yang Sudah Disesuaikan:

### **CSS Updates:**
1. ✅ Responsive media queries (@media max-width: 575.98px)
2. ✅ Font scaling untuk mobile
3. ✅ Touch targets 44px minimum
4. ✅ Button stacking (vertical)
5. ✅ Table responsive dengan horizontal scroll
6. ✅ Card padding adjustments
7. ✅ Grid system responsive

### **View Files - UPDATED:**
1. ✅ **dashboard/admin.blade.php**
   - Stat cards: `col-6 col-md-3` (2 kolom mobile, 4 desktop)
   - Tabel jadwal: `d-none d-md-table-cell` untuk kolom Keperluan
   - Mobile info: Tampil di kolom Ruangan saat mobile
   - Action cards: `col-12 col-md-4` (full width mobile)
   - Buttons: `flex-column flex-md-row` (stack vertical mobile)

2. ✅ **dashboard/peminjam.blade.php**
   - Action cards: `col-12 col-md-4`
   - Buttons: `w-100` untuk full width di mobile

3. ✅ **jadwal/index.blade.php**
   - Filter form: `col-12 col-md-4`
   - Button filter: `w-100`

4. ✅ **peminjam/peminjaman/create.blade.php**
   - Form container: `col-12 col-lg-8`
   - Form fields: `col-12 col-md-6`
   - Info sidebar: `col-12 col-lg-4`
   - Buttons: `flex-column flex-md-row`

5. ✅ **peminjam/peminjaman/index.blade.php**
   - Header buttons: `flex-column flex-md-row`
   - Table columns: `d-none d-md-table-cell` untuk #, Tanggal
   - Mobile info: Tanggal & waktu tampil di kolom Ruangan
   - Action buttons: `flex-column flex-md-row gap-1`

6. ✅ **admin/laporan/index.blade.php**
   - Filter form: `col-12 col-md-4`
   - Stat cards: `col-6 col-sm-4 col-md-2` (2 mobile, 3 tablet, 6 desktop)
   - Export buttons: `flex-column flex-md-row`

---

## 📋 View Files Yang Perlu Disesuaikan:

### **Priority 1 - Critical Pages (User Facing)**

#### 1. **Login & Register**
**Files:**
- `resources/views/auth/login.blade.php` ✅ (Sudah ada responsive CSS)
- `resources/views/auth/register.blade.php` ✅ (Sudah ada responsive CSS)

**Status:** ✅ DONE

---

#### 2. **Dashboard Pages**
**Files:**
- `resources/views/dashboard/admin.blade.php` ⚠️ Perlu update
- `resources/views/dashboard/petugas.blade.php` ⚠️ Perlu update
- `resources/views/dashboard/peminjam.blade.php` ⚠️ Perlu update

**Yang Perlu Ditambahkan:**
```blade
<!-- Ganti col-md-4 dengan col-6 col-md-4 untuk mobile 2 kolom -->
<div class="col-6 col-md-4">
    <div class="action-card">...</div>
</div>

<!-- Statistik cards -->
<div class="col-6 col-md-3">
    <div class="stat-card">...</div>
</div>

<!-- Buttons group - tambahkan d-flex flex-column flex-md-row -->
<div class="d-flex flex-column flex-md-row gap-2">
    <a href="#" class="btn btn-primary">...</a>
</div>
```

---

#### 3. **Jadwal Ruangan**
**File:** `resources/views/jadwal/index.blade.php`

**Yang Perlu Ditambahkan:**
```blade
<!-- Table responsive wrapper -->
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <!-- Hide di mobile, show di desktop -->
                <th class="d-none d-md-table-cell">Detail Info</th>
                <th>Ruangan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="d-none d-md-table-cell">...</td>
                <td>
                    <strong>{{ $ruang->nama_ruang }}</strong>
                    <!-- Show info di mobile yang hidden di desktop -->
                    <small class="d-md-none d-block">
                        Detail lengkap...
                    </small>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Filter form - stack vertically on mobile -->
<div class="row g-2">
    <div class="col-12 col-md-4">
        <input type="date" class="form-control">
    </div>
    <div class="col-12 col-md-4">
        <select class="form-select">...</select>
    </div>
    <div class="col-12 col-md-4">
        <button class="btn btn-primary w-100">Filter</button>
    </div>
</div>
```

**Status:** ⚠️ Perlu update

---

#### 4. **Peminjaman Pages**
**Files:**
- `resources/views/peminjam/peminjaman/create.blade.php` ⚠️ Perlu update
- `resources/views/peminjam/peminjaman/index.blade.php` ⚠️ Perlu update
- `resources/views/peminjam/peminjaman/show.blade.php` ⚠️ Perlu update

**Yang Perlu Ditambahkan:**
```blade
<!-- Form layout responsive -->
<div class="row g-3">
    <div class="col-12 col-md-6">
        <label class="form-label">Label</label>
        <input type="text" class="form-control">
    </div>
</div>

<!-- Info cards responsive -->
<div class="col-12 col-md-4">
    <div class="info-card">...</div>
</div>

<!-- Action buttons responsive -->
<div class="d-flex flex-column flex-md-row gap-2">
    <button class="btn btn-primary">Submit</button>
    <a href="#" class="btn btn-secondary">Cancel</a>
</div>
```

**Status:** ⚠️ Perlu update

---

#### 5. **Persetujuan Pages (Admin/Petugas)**
**File:** `resources/views/admin/persetujuan/index.blade.php`

**Yang Perlu Ditambahkan:**
```blade
<!-- Cards responsive -->
<div class="col-12 col-md-6 col-lg-4">
    <div class="persetujuan-card">...</div>
</div>

<!-- Detail table mobile friendly -->
<div class="table-responsive">
    <table class="table">
        <tr>
            <th style="width: 40%;">Label</th>
            <td>Value</td>
        </tr>
    </table>
</div>
```

**Status:** ⚠️ Perlu update

---

#### 6. **Laporan Pages**
**File:** `resources/views/admin/laporan/index.blade.php`

**Yang Perlu Ditambahkan:**
```blade
<!-- Stat cards responsive -->
<div class="col-6 col-sm-4 col-md-2">
    <div class="stat-card">...</div>
</div>

<!-- Export buttons responsive -->
<div class="d-flex flex-column flex-md-row gap-2">
    <a href="#" class="btn btn-success">
        <i class="fas fa-file-excel"></i>
        <span class="d-none d-md-inline"> Export</span> Excel
    </a>
</div>
```

**Status:** ⚠️ Perlu update

---

#### 7. **Ruang Management (Admin)**
**Files:**
- `resources/views/admin/ruang/index.blade.php` ⚠️ Perlu update
- `resources/views/admin/ruang/create.blade.php` ⚠️ Perlu update
- `resources/views/admin/ruang/edit.blade.php` ⚠️ Perlu update

**Status:** ⚠️ Perlu update

---

#### 8. **User Management (Admin)**
**Files:**
- `resources/views/admin/users/index.blade.php` ⚠️ Perlu update
- `resources/views/admin/users/create.blade.php` ⚠️ Perlu update
- `resources/views/admin/users/edit.blade.php` ⚠️ Perlu update

**Status:** ⚠️ Perlu update

---

### **Priority 2 - Layout Components**

#### 9. **Main Layout**
**File:** `resources/views/layouts/app.blade.php`

**Checklist:**
- ✅ Viewport meta tag
- ⚠️ Navbar responsive (hamburger menu)
- ⚠️ Sidebar collapse on mobile (if any)
- ⚠️ Footer responsive

---

## 🎯 Common Responsive Patterns to Apply:

### **1. Grid Columns:**
```blade
<!-- 2 columns on mobile, 3 on tablet, 4 on desktop -->
<div class="col-6 col-sm-4 col-md-3">

<!-- 1 column on mobile, 2 on tablet, 3 on desktop -->
<div class="col-12 col-sm-6 col-md-4">

<!-- Full width on mobile, half on desktop -->
<div class="col-12 col-md-6">
```

### **2. Hide/Show Elements:**
```blade
<!-- Hide on mobile, show on desktop -->
<div class="d-none d-md-block">Desktop only content</div>

<!-- Show on mobile, hide on desktop -->
<div class="d-md-none">Mobile only content</div>

<!-- Table columns -->
<th class="d-none d-md-table-cell">Desktop Column</th>
```

### **3. Button Groups:**
```blade
<!-- Stack vertically on mobile -->
<div class="d-flex flex-column flex-md-row gap-2">
    <button class="btn btn-primary">Button 1</button>
    <button class="btn btn-secondary">Button 2</button>
</div>
```

### **4. Text Alignment:**
```blade
<!-- Center on mobile, left on desktop -->
<div class="text-center text-md-start">

<!-- Left on mobile, right on desktop -->
<div class="text-start text-md-end">
```

### **5. Spacing:**
```blade
<!-- Larger spacing on desktop -->
<div class="py-3 py-md-4">

<!-- No margin on mobile, margin on desktop -->
<div class="mb-0 mb-md-3">
```

---

## 🔧 Testing Checklist:

### **Devices to Test:**
- [ ] iPhone SE (375px)
- [ ] iPhone 12 Pro (390px)
- [ ] iPhone 14 Pro Max (430px)
- [ ] Samsung Galaxy S21 (360px)
- [ ] iPad Mini (768px)
- [ ] iPad Pro (1024px)

### **Browser DevTools:**
```
1. Chrome: F12 → Toggle device toolbar (Ctrl+Shift+M)
2. Firefox: F12 → Responsive Design Mode (Ctrl+Shift+M)
3. Safari: Develop → Enter Responsive Design Mode
```

### **Test Scenarios:**
- [ ] Navigation menu works on mobile
- [ ] Forms are easy to fill on mobile
- [ ] Tables scroll horizontally
- [ ] Buttons are tappable (44px touch target)
- [ ] Text is readable (min 14px)
- [ ] Images scale properly
- [ ] No horizontal scroll
- [ ] Cards don't overlap
- [ ] Modals fit screen
- [ ] Alerts are readable

---

## 📝 Quick Fixes Applied:

1. ✅ Added comprehensive media queries
2. ✅ Touch-friendly button sizes (44px minimum)
3. ✅ Font scaling for mobile
4. ✅ Card padding adjustments
5. ✅ Table responsive wrappers
6. ✅ Button group stacking
7. ✅ Spacing optimizations

---

## 🚀 Next Steps:

1. **Update all dashboard views** dengan grid responsive
2. **Update table views** dengan d-none d-md-table-cell
3. **Update form layouts** dengan col-12 col-md-6
4. **Update button groups** dengan flex-column flex-md-row
5. **Test semua pages** di mobile browser
6. **Fix issues** yang ditemukan saat testing

---

**Last Updated:** 2025-11-14
**Status:** In Progress (CSS Done, Views Need Update)

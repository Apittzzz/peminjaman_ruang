# 🛠️ Scripts Directory

Folder ini berisi script-script development dan testing yang tidak masuk ke production.

## 📂 Struktur

```
scripts/
├── test_scripts/          # Script untuk testing fitur
│   ├── create_test_users.php
│   ├── setup_jadwal_test.php
│   ├── test_excel_export.php
│   ├── test_room_relocation.php
│   └── VERIFIKASI_JADWAL_RELOKASI.md
└── dev_tools/            # Tools untuk development
```

## 🧪 Test Scripts

### `create_test_users.php`
**Fungsi:** Membuat user dummy untuk testing  
**Cara Pakai:**
```bash
php scripts/test_scripts/create_test_users.php
```

### `setup_jadwal_test.php`
**Fungsi:** Setup data jadwal untuk testing  
**Cara Pakai:**
```bash
php scripts/test_scripts/setup_jadwal_test.php
```

### `test_excel_export.php`
**Fungsi:** Testing export Excel functionality  
**Cara Pakai:**
```bash
php scripts/test_scripts/test_excel_export.php
```

### `test_room_relocation.php`
**Fungsi:** Testing automatic room relocation feature  
**Cara Pakai:**
```bash
php scripts/test_scripts/test_room_relocation.php
```

## ⚠️ PENTING

**Jangan jalankan script ini di production!**  
Script ini hanya untuk development dan testing.

## 📝 Catatan

- Semua script diasumsikan dijalankan dari root project
- Pastikan `.env` sudah dikonfigurasi dengan benar
- Database harus sudah di-migrate sebelum menjalankan script

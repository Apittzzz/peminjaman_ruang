<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\PersetujuanUmumController;
use App\Http\Controllers\Peminjam\PeminjamanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RuangController;
use App\Http\Controllers\Admin\LaporanController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\Peminjaman;
use Carbon\Carbon;

// Public routes
Route::get('/', function () {
    return view('auth.login');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// Protected routes
Route::middleware('auth')->group(function () 
{
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    
    // Jadwal routes (accessible by all authenticated users)
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
    Route::get('/jadwal/calendar', [JadwalController::class, 'calendar'])->name('jadwal.calendar');
    
    // Persetujuan Umum routes (untuk admin dan petugas)
    Route::middleware(['auth', 'petugas'])->group(function () {

    // Tampilkan halaman persetujuan (menggunakan controller)
    Route::get('/persetujuan', [PersetujuanUmumController::class, 'index'])
        ->name('persetujuan.index');

    // Aksi approve / reject
    Route::post('/persetujuan/{peminjaman}/approve', [PersetujuanUmumController::class, 'approve'])
        ->name('persetujuan.approve');

    Route::post('/persetujuan/{peminjaman}/reject', [PersetujuanUmumController::class, 'reject'])
        ->name('persetujuan.reject');
});

    // Admin routes
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        
        // Manajemen User
        Route::resource('users', UserController::class);
        
        // Manajemen Ruang
        Route::resource('ruang', RuangController::class);

        // Manajemen Peminjaman
        Route::get('/peminjaman', [PersetujuanUmumController::class, 'index'])->name('peminjaman.index');

        // Laporan routes
        Route::get('/laporan', [\App\Http\Controllers\Admin\LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export', [\App\Http\Controllers\Admin\LaporanController::class, 'export'])->name('laporan.export');
    });
    
    // Petugas routes
    Route::middleware(['petugas'])->prefix('petugas')->name('petugas.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'petugas'])->name('dashboard');

        // allow petugas to manually mark a peminjaman as finished
        Route::post('/peminjaman/{peminjaman}/complete', function ($peminjamanId) {
            DB::table('peminjaman')
                ->where('id_peminjaman', $peminjamanId)
                ->update(['status' => 'selesai', 'updated_at' => now()]);

            return back()->with('success', 'Peminjaman telah ditandai selesai.');
        })->name('peminjaman.complete');

        // Laporan routes
        Route::get('/laporan', [\App\Http\Controllers\Admin\LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export', [\App\Http\Controllers\Admin\LaporanController::class, 'export'])->name('laporan.export');
    });
    
    // Peminjam routes
    Route::middleware(['peminjam'])->prefix('peminjam')->name('peminjam.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'peminjam'])->name('dashboard');
        
        // Peminjaman routes
        Route::resource('peminjaman', PeminjamanController::class);
        Route::post('/peminjaman/{peminjaman}/cancel', [PeminjamanController::class, 'cancel'])
            ->name('peminjaman.cancel');
    });
});
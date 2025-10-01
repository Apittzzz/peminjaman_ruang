<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// Protected routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    
    // Admin routes
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    
    // Manajemen User
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    
    // Manajemen Ruang
    Route::resource('ruang', \App\Http\Controllers\Admin\RuangController::class);
});
    
    // Petugas routes
    Route::middleware('petugas')->prefix('petugas')->name('petugas.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'petugas'])->name('dashboard');
        // Tambahkan route petugas lainnya di sini
    });
    
    // Peminjam routes
    Route::middleware('peminjam')->prefix('peminjam')->name('peminjam.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'peminjam'])->name('dashboard');
        // Tambahkan route peminjam lainnya di sini
    });
});
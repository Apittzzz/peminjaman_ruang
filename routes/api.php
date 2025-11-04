<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PeminjamanController as ApiPeminjamanController;
use App\Http\Controllers\Api\RuangController as ApiRuangController;
use App\Http\Controllers\Api\JadwalController as ApiJadwalController;
use App\Http\Controllers\Api\PersetujuanController as ApiPersetujuanController;
use App\Http\Controllers\Api\UserController as ApiUserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Authentication routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User Profile routes
    Route::get('/profile', [ApiUserController::class, 'profile']);
    Route::put('/profile', [ApiUserController::class, 'updateProfile']);
    Route::get('/statistics', [ApiUserController::class, 'statistics']);

    // Peminjaman routes
    Route::apiResource('peminjaman', ApiPeminjamanController::class);

    // Ruang routes
    Route::apiResource('ruang', ApiRuangController::class);
    Route::post('/ruang/check-availability', [ApiRuangController::class, 'checkAvailability']);

    // Jadwal routes
    Route::get('/jadwal', [ApiJadwalController::class, 'index']);
    Route::get('/jadwal/calendar', [ApiJadwalController::class, 'calendar']);

    // Persetujuan routes (for petugas and admin)
    Route::get('/persetujuan', [ApiPersetujuanController::class, 'index']);
    Route::get('/persetujuan/{peminjaman}', [ApiPersetujuanController::class, 'show']);
    Route::post('/persetujuan/{id}/approve', [ApiPersetujuanController::class, 'approve']);
    Route::post('/persetujuan/{id}/reject', [ApiPersetujuanController::class, 'reject']);
    Route::post('/persetujuan/{id}/complete', [ApiPersetujuanController::class, 'complete']);
});
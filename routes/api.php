<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PeminjamanController as ApiPeminjamanController;
use App\Http\Controllers\Api\RuangController as ApiRuangController;
use App\Http\Controllers\Api\JadwalController as ApiJadwalController;

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
    // Peminjaman routes
    Route::apiResource('peminjaman', ApiPeminjamanController::class);

    // Ruang routes
    Route::apiResource('ruang', ApiRuangController::class);

    // Jadwal routes
    Route::get('/jadwal', [ApiJadwalController::class, 'index']);
    Route::get('/jadwal/calendar', [ApiJadwalController::class, 'calendar']);
});
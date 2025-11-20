<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        // ========================================
        // TEST FUNCTION: count_peminjaman_by_status
        // ========================================
        
        // Cara 1: Menggunakan DB::select (Recommended)
        $pendingCount = DB::select('SELECT count_peminjaman_by_status(?) AS total', ['pending'])[0]->total;
        $approvedCount = DB::select('SELECT count_peminjaman_by_status(?) AS total', ['approved'])[0]->total;
        $rejectedCount = DB::select('SELECT count_peminjaman_by_status(?) AS total', ['rejected'])[0]->total;
        $selesaiCount = DB::select('SELECT count_peminjaman_by_status(?) AS total', ['selesai'])[0]->total;
        
        // Total peminjaman
        $totalPeminjaman = $pendingCount + $approvedCount + $rejectedCount + $selesaiCount;
        
        // Get all peminjaman with pagination
        $peminjaman = Peminjaman::with(['user', 'ruang'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.laporan.index', compact(
            'peminjaman',
            'totalPeminjaman',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'selesaiCount'
        ));
    }
    
    public function checkAvailability()
    {
        // ========================================
        // TEST FUNCTION: check_room_availability
        // ========================================
        
        $ruangId = request('ruang_id');
        $tanggal = request('tanggal');
        $waktuMulai = request('waktu_mulai');
        $waktuSelesai = request('waktu_selesai');
        
        $availability = DB::select(
            'SELECT check_room_availability(?, ?, ?, ?) AS status',
            [$ruangId, $tanggal, $waktuMulai, $waktuSelesai]
        )[0]->status;
        
        return response()->json([
            'available' => $availability === 'TERSEDIA',
            'message' => $availability
        ]);
    }
}
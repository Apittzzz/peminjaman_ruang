<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;

/**
 * Dashboard Controller
 * 
 * Handles dashboard views for different user roles (Admin, Petugas, Peminjam)
 * 
 * @package App\Http\Controllers
 * @author Apittzzz
 * @version 1.0.0
 */
class DashboardController extends Controller
{
    /**
     * Display Admin Dashboard
     * 
     * @return \Illuminate\View\View
     */
    public function admin()
    {
        // Statistik untuk dashboard
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_ruang' => \App\Models\Ruang::count(),
            'ruang_kosong' => \App\Models\Ruang::where('status', 'kosong')
                ->whereNull('pengguna_default')
                ->count(),
            'ruang_dipakai' => \App\Models\Ruang::where(function($q) {
                $q->where('status', 'dipakai')
                  ->orWhereNotNull('pengguna_default');
            })->count(),
            'pending_peminjaman' => Peminjaman::where('status', 'pending')->count(),
            'approved_peminjaman' => Peminjaman::where('status', 'approved')->count(),
        ];
        
        // Peminjaman hari ini
        $todayBookings = Peminjaman::with(['user', 'ruang'])
            ->whereDate('tanggal_pinjam', '<=', today())
            ->whereDate('tanggal_kembali', '>=', today())
            ->where('status', 'approved')
            ->orderBy('waktu_mulai')
            ->limit(5)
            ->get();
        
        return view('dashboard.admin', compact('stats', 'todayBookings'));
    }
    
    /**
     * Display Petugas Dashboard
     * 
     * Shows pending bookings count and list for review
     * 
     * @return \Illuminate\View\View
     */
    public function petugas()
    {
        $pendingCount = Peminjaman::where('status', 'pending')->count();
        $approvedCount = Peminjaman::where('status', 'approved')->count();
        $pendingPeminjaman = Peminjaman::with(['user', 'ruang'])
            ->where('status', 'pending')
            ->latest()
            ->get();
        
        return view('dashboard.petugas', compact('pendingCount', 'approvedCount', 'pendingPeminjaman'));
    }
    
    /**
     * Display Peminjam Dashboard
     * 
     * @return \Illuminate\View\View
     */
    public function peminjam()
    {
        return view('dashboard.peminjam');
    }
}
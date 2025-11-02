<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;

class DashboardController extends Controller
{
    public function admin()
    {
        return view('dashboard.admin');
    }
    
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
    
    public function peminjam()
    {
        return view('dashboard.peminjam');
    }
}
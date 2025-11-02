<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Peminjaman;
use App\Models\Ruang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersetujuanController extends Controller
{
    /**
     * Constructor - apply middleware
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check authorization
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'petugas') {
            abort(403, 'Unauthorized access.');
        }

        // Log untuk debug
        \Log::info('Persetujuan index accessed by: ' . Auth::user()->username);
        
        $peminjaman = Peminjaman::with(['user', 'ruang'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();
            
        $riwayat = Peminjaman::with(['user', 'ruang'])
            ->where('status', '!=', 'pending')
            ->orderBy('updated_at', 'desc')
            ->take(20)
            ->get();

        return view('admin.persetujuan.index', compact('peminjaman', 'riwayat'));
    }

    /**
     * Approve peminjaman
     */
    public function approve($id)
    {
        // Check authorization
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'petugas') {
            abort(403, 'Unauthorized access.');
        }

        // Log untuk debug
        \Log::info('Approve attempt by: ' . Auth::user()->username . ' for peminjaman: ' . $id);
        
        $peminjaman = Peminjaman::with('ruang')->findOrFail($id);
        
        $peminjaman->update([
            'status' => 'approved',
            'catatan' => request('catatan', null)
        ]);

        // Update status ruang menjadi dipakai
        $peminjaman->ruang->update(['status' => 'dipakai']);

        \Log::info('Peminjaman approved: ' . $id);

        return redirect()->route('admin.persetujuan.index')
            ->with('success', 'Peminjaman berhasil disetujui.');
    }

    /**
     * Reject peminjaman
     */
    public function reject($id)
    {
        // Check authorization
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'petugas') {
            abort(403, 'Unauthorized access.');
        }

        $request = request();
        $request->validate([
            'catatan' => 'required|string|max:500'
        ]);

        $peminjaman = Peminjaman::findOrFail($id);

        $peminjaman->update([
            'status' => 'rejected',
            'catatan' => $request->catatan
        ]);

        \Log::info('Peminjaman rejected: ' . $id);

        return redirect()->route('admin.persetujuan.index')
            ->with('success', 'Peminjaman berhasil ditolak.');
    }

    /**
     * Show peminjaman details
     */
    public function show($id)
    {
        // Check authorization
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'petugas') {
            abort(403, 'Unauthorized access.');
        }

        $peminjaman = Peminjaman::with(['user', 'ruang'])->findOrFail($id);
        return view('admin.persetujuan.show', compact('peminjaman'));
    }

    /**
     * Update status ruang setelah peminjaman selesai
     */
    public function selesai($id)
    {
        // Check authorization
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'petugas') {
            abort(403, 'Unauthorized access.');
        }

        $peminjaman = Peminjaman::with('ruang')->findOrFail($id);
        
        // Kembalikan status ruang menjadi kosong
        $peminjaman->ruang->update(['status' => 'kosong']);
        
        // Update status peminjaman
        $peminjaman->update(['status' => 'completed']);

        return redirect()->back()->with('success', 'Status ruang telah dikembalikan menjadi kosong.');
    }
}
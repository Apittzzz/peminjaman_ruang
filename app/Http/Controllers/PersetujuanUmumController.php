<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Ruang;
use App\Services\RoomRelocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersetujuanUmumController extends Controller
{
    public function index()
    {
        // Debug: Cek user dan session
        \Log::info('User accessing persetujuan:', [
            'user_id' => Auth::id(),
            'username' => Auth::user()->username,
            'role' => Auth::user()->role,
            'session_id' => session()->getId()
        ]);

        $peminjaman = Peminjaman::with(['user', 'ruang'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();
            
        $riwayat = Peminjaman::with(['user', 'ruang'])
            ->where('status', '!=', 'pending')
            ->orderBy('updated_at', 'desc')
            ->take(20)
            ->get();

        return view('persetujuan.index', compact('peminjaman', 'riwayat'));
    }

    public function approve(Request $request, $id)
    {
        \Log::info('Approve attempt:', [
            'user' => Auth::user()->username,
            'peminjaman_id' => $id
        ]);

        $peminjaman = Peminjaman::with('ruang')->findOrFail($id);
        
        $peminjaman->update([
            'status' => 'approved',
            'catatan' => $request->catatan
        ]);

        $peminjaman->ruang->update(['status' => 'dipakai']);

        // Pindahkan pengguna default jika ada
        $relocationService = new RoomRelocationService();
        $relocationResult = $relocationService->relocateDefaultUser($peminjaman);
        
        $message = 'Peminjaman berhasil disetujui.';
        if ($relocationResult['relocated']) {
            $message .= ' ' . $relocationResult['message'];
        }

        return redirect()->back()->with('success', $message);
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string|max:500'
        ]);

        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->update([
            'status' => 'rejected',
            'catatan' => $request->catatan
        ]);

        return redirect()->back()->with('success', 'Peminjaman berhasil ditolak.');
    }
}
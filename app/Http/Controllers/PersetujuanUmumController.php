<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PersetujuanUmumController extends Controller
{
    public function index()
    {
        $peminjaman = Peminjaman::with(['user', 'ruang'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.persetujuan.index', compact('peminjaman'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:500'
        ]);

        try {
            // Call stored procedure
            DB::statement('CALL approve_peminjaman(?, ?, ?)', [
                $id,
                $request->catatan ?? 'Peminjaman disetujui',
                Auth::id()
            ]);

            return redirect()->route('admin.persetujuan.index')
                ->with('success', 'Peminjaman berhasil disetujui menggunakan Stored Procedure');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menyetujui peminjaman: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function approveManual(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:500'
        ]);

        // START TRANSACTION
        DB::beginTransaction();

        try {
            // Step 1: Update peminjaman
            $peminjaman = Peminjaman::with(['user', 'ruang'])->findOrFail($id);
            $peminjaman->status = 'approved';
            $peminjaman->catatan = $request->catatan ?? 'Peminjaman disetujui';
            $peminjaman->approved_at = now();
            $peminjaman->save();

            // Step 2: Update room status
            $peminjaman->ruang->status = 'dipakai';
            $peminjaman->ruang->save();

            // Step 3: Create notification
            $peminjaman->user->notifications()->create([
                'title' => 'Peminjaman Disetujui',
                'message' => "Peminjaman ruangan {$peminjaman->ruang->nama_ruang} telah disetujui",
                'type' => 'approval'
            ]);

            // COMMIT if all success
            DB::commit();

            return redirect()->route('admin.persetujuan.index')
                ->with('success', 'Peminjaman berhasil disetujui dengan Manual Transaction');

        } catch (\Exception $e) {
            // ROLLBACK if error
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => 'Gagal menyetujui peminjaman: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // ========================================
    // METHOD 3: Reject dengan STORED PROCEDURE
    // ========================================
    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string|max:500'
        ]);

        try {
            // Call stored procedure
            DB::statement('CALL reject_peminjaman(?, ?, ?)', [
                $id,
                $request->catatan,
                Auth::id()
            ]);

            return redirect()->route('admin.persetujuan.index')
                ->with('success', 'Peminjaman berhasil ditolak');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menolak peminjaman: ' . $e->getMessage()])
                ->withInput();
        }
    }
}
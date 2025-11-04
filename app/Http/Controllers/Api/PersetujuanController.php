<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PersetujuanController extends Controller
{
    /**
     * Get all pending peminjaman (for petugas and admin)
     */
    public function index(Request $request)
    {
        // Check if user is petugas or admin
        if (! in_array(auth()->user()->role, ['petugas', 'admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only petugas and admin can access this endpoint.',
            ], 403);
        }

        $status = $request->get('status', 'pending');

        $peminjaman = Peminjaman::with(['user', 'ruang'])
            ->when($status !== 'all', function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $peminjaman,
        ]);
    }

    /**
     * Get single peminjaman detail (for approval review)
     */
    public function show(Peminjaman $peminjaman)
    {
        // Check if user is petugas or admin
        if (! in_array(auth()->user()->role, ['petugas', 'admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $peminjaman->load(['user', 'ruang']),
        ]);
    }

    /**
     * Approve peminjaman
     */
    public function approve(Request $request, $id)
    {
        // Check if user is petugas or admin
        if (! in_array(auth()->user()->role, ['petugas', 'admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only petugas and admin can approve peminjaman.',
            ], 403);
        }

        $peminjaman = Peminjaman::findOrFail($id);

        // Check if peminjaman is still pending
        if ($peminjaman->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman sudah diproses sebelumnya',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'catatan' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $peminjaman->update([
            'status' => 'approved',
            'catatan' => $request->catatan,
        ]);

        // Update room status to 'dipakai' if needed
        if ($peminjaman->ruang) {
            $peminjaman->ruang->update(['status' => 'dipakai']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil disetujui',
            'data' => $peminjaman->load(['user', 'ruang']),
        ]);
    }

    /**
     * Reject peminjaman
     */
    public function reject(Request $request, $id)
    {
        // Check if user is petugas or admin
        if (! in_array(auth()->user()->role, ['petugas', 'admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only petugas and admin can reject peminjaman.',
            ], 403);
        }

        $peminjaman = Peminjaman::findOrFail($id);

        // Check if peminjaman is still pending
        if ($peminjaman->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman sudah diproses sebelumnya',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'catatan' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $peminjaman->update([
            'status' => 'rejected',
            'catatan' => $request->catatan,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil ditolak',
            'data' => $peminjaman->load(['user', 'ruang']),
        ]);
    }

    /**
     * Mark peminjaman as completed
     */
    public function complete($id)
    {
        // Check if user is petugas or admin
        if (! in_array(auth()->user()->role, ['petugas', 'admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only petugas and admin can complete peminjaman.',
            ], 403);
        }

        $peminjaman = Peminjaman::findOrFail($id);

        // Check if peminjaman is approved
        if ($peminjaman->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Only approved peminjaman can be marked as completed',
            ], 422);
        }

        $peminjaman->update(['status' => 'selesai']);

        // Update room status back to 'tersedia'
        if ($peminjaman->ruang) {
            $peminjaman->ruang->update(['status' => 'tersedia']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil ditandai selesai',
            'data' => $peminjaman->load(['user', 'ruang']),
        ]);
    }
}

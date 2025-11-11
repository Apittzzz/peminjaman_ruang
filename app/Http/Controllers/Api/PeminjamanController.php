<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Ruang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $peminjaman = Peminjaman::with('ruang')
            ->where('id_user', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $statusCount = [
            'pending' => $peminjaman->where('status', 'pending')->count(),
            'approved' => $peminjaman->where('status', 'approved')->count(),
            'selesai' => $peminjaman->where('status', 'selesai')->count(),
            'cancelled' => $peminjaman->where('status', 'cancelled')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'peminjaman' => $peminjaman,
                'status_count' => $statusCount
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_ruang' => 'required|exists:ruang,id_ruang',
            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i',
            'keperluan' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validasi khusus: jika tanggal sama, waktu selesai harus lebih dari waktu mulai
        if ($request->tanggal_pinjam === $request->tanggal_kembali) {
            if ($request->waktu_selesai <= $request->waktu_mulai) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pada hari yang sama, waktu selesai harus lebih dari waktu mulai'
                ], 422);
            }
        }

        // Validate time range
        $conflict = Peminjaman::where('id_ruang', $request->id_ruang)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($request) {
                $query->whereBetween('tanggal_pinjam', [$request->tanggal_pinjam, $request->tanggal_kembali])
                      ->orWhereBetween('tanggal_kembali', [$request->tanggal_pinjam, $request->tanggal_kembali])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('tanggal_pinjam', '<=', $request->tanggal_pinjam)
                            ->where('tanggal_kembali', '>=', $request->tanggal_kembali);
                      });
            })
            ->where(function ($query) use ($request) {
                $query->whereBetween('waktu_mulai', [$request->waktu_mulai, $request->waktu_selesai])
                      ->orWhereBetween('waktu_selesai', [$request->waktu_mulai, $request->waktu_selesai])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('waktu_mulai', '<=', $request->waktu_mulai)
                            ->where('waktu_selesai', '>=', $request->waktu_selesai);
                      });
            })
            ->exists();

        if ($conflict) {
            return response()->json([
                'success' => false,
                'message' => 'Ruangan sudah dipesan pada waktu tersebut'
            ], 422);
        }

        $peminjaman = Peminjaman::create([
            'id_user' => Auth::id(),
            'id_ruang' => $request->id_ruang,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali' => $request->tanggal_kembali,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'keperluan' => $request->keperluan,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil diajukan',
            'data' => $peminjaman->load('ruang')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Peminjaman $peminjaman)
    {
        // Check if user owns this peminjaman
        if ($peminjaman->id_user !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $peminjaman->load('ruang', 'user')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Peminjaman $peminjaman)
    {
        // Check if user owns this peminjaman
        if ($peminjaman->id_user !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Only allow update if status is pending
        if ($peminjaman->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman tidak dapat diubah'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'id_ruang' => 'sometimes|exists:ruang,id_ruang',
            'tanggal_pinjam' => 'sometimes|date|after_or_equal:today',
            'tanggal_kembali' => 'sometimes|date|after_or_equal:tanggal_pinjam',
            'waktu_mulai' => 'sometimes|date_format:H:i|before:waktu_selesai',
            'waktu_selesai' => 'sometimes|date_format:H:i|after:waktu_mulai',
            'keperluan' => 'sometimes|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $peminjaman->update($request->only([
            'id_ruang', 'tanggal_pinjam', 'tanggal_kembali', 'waktu_mulai', 'waktu_selesai', 'keperluan'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil diupdate',
            'data' => $peminjaman->load('ruang')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Peminjaman $peminjaman)
    {
        // Check if user owns this peminjaman
        if ($peminjaman->id_user !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Only allow cancel if status is pending
        if ($peminjaman->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman tidak dapat dibatalkan'
            ], 422);
        }

        $peminjaman->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil dibatalkan'
        ]);
    }
}
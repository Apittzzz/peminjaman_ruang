<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Ruang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RuangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ruangs = Ruang::all();

        return response()->json([
            'success' => true,
            'data' => $ruangs,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Only admin can create rooms
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $request->validate([
            'nama_ruang' => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:1',
            'status' => 'required|in:tersedia,tidak tersedia',
        ]);

        $ruang = Ruang::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Ruang berhasil ditambahkan',
            'data' => $ruang,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Ruang $ruang)
    {
        return response()->json([
            'success' => true,
            'data' => $ruang,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ruang $ruang)
    {
        // Only admin can update rooms
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $request->validate([
            'nama_ruang' => 'sometimes|string|max:255',
            'kapasitas' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:tersedia,tidak tersedia',
        ]);

        $ruang->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Ruang berhasil diupdate',
            'data' => $ruang,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ruang $ruang)
    {
        // Only admin can delete rooms
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $ruang->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ruang berhasil dihapus',
        ]);
    }

    /**
     * Check room availability for specific date and time range
     */
    public function checkAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_ruang' => 'required|exists:ruang,id_ruang',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check for conflicting bookings
        $conflict = Peminjaman::where('id_ruang', $request->id_ruang)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'rejected')
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

        $available = ! $conflict;

        return response()->json([
            'success' => true,
            'data' => [
                'available' => $available,
                'message' => $available
                    ? 'Ruangan tersedia pada waktu yang dipilih'
                    : 'Ruangan tidak tersedia pada waktu yang dipilih',
            ],
        ]);
    }
}

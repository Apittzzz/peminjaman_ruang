<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ruang;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    /**
     * Display jadwal ruang
     */
    public function index(Request $request)
    {
        $selectedTanggal = $request->get('tanggal', date('Y-m-d'));
        $lihatSemua = $request->get('semua');

        $ruangs = Ruang::with(['peminjaman' => function ($query) use ($selectedTanggal, $lihatSemua) {
            $query->where('tanggal_pinjam', '<=', $selectedTanggal)
                  ->where('tanggal_kembali', '>=', $selectedTanggal);
            if (!$lihatSemua) {
                $query->where('status', 'approved');
            }
        }, 'peminjaman.user'])->get();

        return response()->json([
            'success' => true,
            'data' => $ruangs,
            'selected_tanggal' => $selectedTanggal
        ]);
    }

    /**
     * Get calendar data
     */
    public function calendar(Request $request)
    {
        $start = $request->get('start', now()->startOfMonth()->toDateString());
        $end = $request->get('end', now()->endOfMonth()->toDateString());

        $peminjaman = Peminjaman::with(['user', 'ruang'])
            ->whereBetween('tanggal_pinjam', [$start, $end])
            ->where('status', 'approved')
            ->get();

        return response()->json($peminjaman);
    }

    /**
     * Get detailed list of current bookings with user information
     */
    public function activeBookings(Request $request)
    {
        $selectedTanggal = $request->get('tanggal', date('Y-m-d'));
        $statusFilter = $request->get('status', 'all'); // all, approved, pending

        $query = Peminjaman::with(['user:id_user,username,nama,email,role', 'ruang:id_ruang,nama_ruang,kapasitas,status,pengguna_default,keterangan_penggunaan'])
            ->where('tanggal_pinjam', '<=', $selectedTanggal)
            ->where('tanggal_kembali', '>=', $selectedTanggal);

        // Filter berdasarkan status jika ditentukan
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $bookings = $query->orderBy('tanggal_pinjam')
                          ->orderBy('waktu_mulai')
                          ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'bookings' => $bookings,
                'total' => $bookings->count(),
                'selected_tanggal' => $selectedTanggal,
                'status_filter' => $statusFilter
            ],
            'message' => 'Daftar peminjaman aktif berhasil diambil'
        ]);
    }
}
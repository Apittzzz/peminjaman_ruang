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
            $query->where('status', 'approved')
                  ->orderBy('waktu_mulai');

            if (!$lihatSemua) {
                $query->where('tanggal_pinjam', '<=', $selectedTanggal)
                      ->where('tanggal_kembali', '>=', $selectedTanggal);
            }
        }, 'peminjaman.user'])->get();

        return response()->json([
            'success' => true,
            'data' => [
                'ruangs' => $ruangs,
                'selected_tanggal' => $selectedTanggal
            ]
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
            ->where('status', 'approved')
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('tanggal_pinjam', [$start, $end])
                      ->orWhereBetween('tanggal_kembali', [$start, $end])
                      ->orWhere(function ($q) use ($start, $end) {
                          $q->where('tanggal_pinjam', '<=', $start)
                            ->where('tanggal_kembali', '>=', $end);
                      });
            })
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id_peminjaman,
                    'title' => $item->ruang->nama_ruang . ' - ' . $item->user->name,
                    'start' => $item->tanggal_pinjam . 'T' . $item->waktu_mulai,
                    'end' => $item->tanggal_kembali . 'T' . $item->waktu_selesai,
                    'backgroundColor' => '#2c3e50',
                    'borderColor' => '#2c3e50',
                ];
            });

        return response()->json($peminjaman);
    }
}

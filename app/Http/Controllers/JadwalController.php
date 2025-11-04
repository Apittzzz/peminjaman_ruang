<?php

namespace App\Http\Controllers;

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

    return view('jadwal.index', compact('ruangs', 'selectedTanggal'));
}
    /*
    public function index(Request $request)
    {
        $selectedRuang = $request->get('ruang');
        $selectedTanggal = $request->get('tanggal', date('Y-m-d'));

        // Ambil semua ruangan
        $ruangs = Ruang::with(['peminjaman' => function ($query) use ($selectedTanggal) {
           $query->where('status', 'approved')
                ->whereDate('tanggal_kembali', '>=', now()->toDateString())
                ->where('tanggal_kembali', '>=', $selectedTanggal)
                ->orderBy('waktu_mulai');
        }, 'peminjaman.user']);

        // Jika user memilih ruangan tertentu, filter
        if ($selectedRuang) {
            $ruangs = $ruangs->where('id_ruang', $selectedRuang);
        }

        $ruangs = $ruangs->get();
        $peminjaman = Peminjaman::with(['user', 'ruang'])
        ->where('status', 'approved')
        ->where('tanggal_pinjam', '<=', $selectedTanggal)
        ->where('tanggal_kembali', '>=', $selectedTanggal)
        ->orderBy('waktu_mulai')
        ->get();

        return view('jadwal.index', compact('ruangs', 'peminjaman', 'selectedRuang', 'selectedTanggal'));
    }
    */

}

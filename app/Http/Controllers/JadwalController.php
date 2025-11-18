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
        $statusFilter = $request->get('status', 'all'); // all, kosong, dipakai

        $ruangs = Ruang::with(['peminjaman' => function ($query) use ($selectedTanggal, $lihatSemua) {
            $query->where('status', 'approved')
                  ->orderBy('waktu_mulai');

            if (!$lihatSemua) {
                $query->where('tanggal_pinjam', '<=', $selectedTanggal)
                      ->where('tanggal_kembali', '>=', $selectedTanggal);
            }
        }, 'peminjaman.user', 'penggunaDefault']); // Load relasi pengguna default

        // Filter berdasarkan status ruangan
        if ($statusFilter === 'kosong') {
            // Ruangan kosong = tidak ada peminjaman DAN tidak ada pengguna default
            $ruangs = $ruangs->where('status', 'kosong')
                             ->whereNull('pengguna_default')
                             ->whereDoesntHave('peminjaman', function ($query) use ($selectedTanggal, $lihatSemua) {
                                 if (!$lihatSemua) {
                                     $query->where('tanggal_pinjam', '<=', $selectedTanggal)
                                           ->where('tanggal_kembali', '>=', $selectedTanggal);
                                 }
                             });
        } elseif ($statusFilter === 'dipakai') {
            // Ruangan dipakai = ada peminjaman ATAU ada pengguna default ATAU is_temporary_occupied
            $ruangs = $ruangs->where(function ($query) use ($selectedTanggal, $lihatSemua) {
                $query->where('status', 'dipakai')
                      ->orWhereNotNull('pengguna_default')
                      ->orWhere('is_temporary_occupied', true)
                      ->orWhereHas('peminjaman', function ($subQuery) use ($selectedTanggal, $lihatSemua) {
                          if (!$lihatSemua) {
                              $subQuery->where('tanggal_pinjam', '<=', $selectedTanggal)
                                       ->where('tanggal_kembali', '>=', $selectedTanggal);
                          }
                      });
            });
        }

        $ruangs = $ruangs->get();

        return view('jadwal.index', compact('ruangs', 'selectedTanggal', 'statusFilter'));
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

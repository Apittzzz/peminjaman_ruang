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
        $ruangs = Ruang::all();
        $selectedRuang = $request->get('ruang');
        $selectedTanggal = $request->get('tanggal', date('Y-m-d'));

        $peminjaman = Peminjaman::with(['user', 'ruang'])
            ->whereIn('status', ['approved'])
            ->when($selectedRuang, function($query) use ($selectedRuang) {
                return $query->where('id_ruang', $selectedRuang);
            })
            ->when($selectedTanggal, function($query) use ($selectedTanggal) {
                return $query->where('tanggal_pinjam', '<=', $selectedTanggal)
                           ->where('tanggal_kembali', '>=', $selectedTanggal);
            })
            ->orderBy('waktu_mulai')
            ->get();

        return view('jadwal.index', compact('ruangs', 'peminjaman', 'selectedRuang', 'selectedTanggal'));
    }

    /**
     * Display calendar view
     */
    public function calendar()
    {
        $ruangs = Ruang::all();
        $events = [];

        $peminjaman = Peminjaman::with(['user', 'ruang'])
            ->whereIn('status', ['approved'])
            ->get();

        foreach ($peminjaman as $item) {
            $events[] = [
                'title' => $item->ruang->nama_ruang . ' - ' . $item->user->nama,
                'start' => $item->tanggal_pinjam . 'T' . $item->waktu_mulai,
                'end' => $item->tanggal_kembali . 'T' . $item->waktu_selesai,
                'color' => $this->getEventColor($item->id_ruang),
            ];
        }

        return view('jadwal.calendar', compact('ruangs', 'events'));
    }

    private function getEventColor($ruangId)
    {
        $colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7', '#DDA0DD', '#98D8C8', '#F7DC6F'];
        return $colors[$ruangId % count($colors)];
    }
}
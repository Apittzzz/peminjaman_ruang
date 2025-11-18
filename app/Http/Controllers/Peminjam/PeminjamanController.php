<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Ruang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        return view('peminjam.peminjaman.index', compact('peminjaman', 'statusCount'));
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create(Request $request)
    {
        $ruangs = Ruang::all(); // ambil semua ruangan
        $selectedRuangId = $request->query('ruang_id'); // ambil ruang_id dari query string
        return view('peminjam.peminjaman.create', compact('ruangs', 'selectedRuangId'));
    }


    /*
    public function create()
    {
        $ruangs = Ruang::where('status', 'kosong')->get();
        return view('peminjam.peminjaman.create', compact('ruangs'));
    }
    */

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi dasar
        $request->validate([
            'id_ruang' => 'required|exists:ruang,id_ruang',
            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'keperluan' => 'required|string|max:500',
        ]);

        // Validasi khusus: jika tanggal sama, waktu selesai harus lebih dari waktu mulai
        if ($request->tanggal_pinjam === $request->tanggal_kembali) {
            if ($request->waktu_selesai <= $request->waktu_mulai) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Pada hari yang sama, waktu selesai harus lebih dari waktu mulai.');
            }
        }

        // Normalisasi waktu
        $start = Carbon::createFromFormat('Y-m-d H:i', $request->tanggal_pinjam . ' ' . $request->waktu_mulai);
        $end = Carbon::createFromFormat('Y-m-d H:i', $request->tanggal_kembali . ' ' . $request->waktu_selesai);
        $now = Carbon::now();

        if ($request->tanggal_pinjam === $now->toDateString() && $start->lessThanOrEqualTo($now)) {
            return redirect()->back()->withInput()->with('error', 'Waktu mulai harus lebih dari waktu sekarang.');
        }

        /*
        try {
            $start = Carbon::createFromFormat('Y-m-d H:i', $request->tanggal_pinjam . ' ' . $request->waktu_mulai);
            $end = Carbon::createFromFormat('Y-m-d H:i', $request->tanggal_pinjam . ' ' . $request->waktu_selesai);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Format waktu tidak valid.');
        }

        $now = Carbon::now();
        */

        // Validasi waktu sudah lewat
        if ($start->lessThanOrEqualTo($now)) {
            return redirect()->back()->withInput()->with('error', 'Waktu mulai sudah lewat. Pilih waktu di masa depan.');
        }

        // Validasi hari Sabtu/Minggu
        $day = $start->dayOfWeekIso; // 6 = Sabtu, 7 = Minggu
        if ($day === 6 || $day === 7) {
            return redirect()->back()->withInput()->with('error', 'Pengajuan pada hari Sabtu atau Minggu tidak diperbolehkan.');
        }

        // Cek ketersediaan ruang
        $isAvailable = Peminjaman::where('id_ruang', $request->id_ruang)
        ->where('tanggal_pinjam', '<=', $request->tanggal_kembali)
        ->where('tanggal_kembali', '>=', $request->tanggal_pinjam)
        ->whereIn('status', ['pending', 'approved'])
        ->where(function($query) use ($start, $end) {
            $query->where(function($q) use ($start, $end) {
                $q->whereRaw("STR_TO_DATE(CONCAT(tanggal_pinjam,' ',waktu_mulai), '%Y-%m-%d %H:%i') < ?", [$end->format('Y-m-d H:i')])
                ->whereRaw("STR_TO_DATE(CONCAT(tanggal_pinjam,' ',waktu_selesai), '%Y-%m-%d %H:%i') > ?", [$start->format('Y-m-d H:i')]);
            });
        })
        ->doesntExist();

        /*
        $isAvailable = Peminjaman::where('id_ruang', $request->id_ruang)
            ->where('tanggal_pinjam', '<=', $request->tanggal_kembali)
            ->where('tanggal_kembali', '>=', $request->tanggal_pinjam)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('waktu_mulai', '<=', $request->waktu_mulai)
                      ->where('waktu_selesai', '>', $request->waktu_mulai);
                })->orWhere(function($q) use ($request) {
                    $q->where('waktu_mulai', '<', $request->waktu_selesai)
                      ->where('waktu_selesai', '>=', $request->waktu_selesai);
                });
            })
            ->doesntExist();
        */
        if (!$isAvailable) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ruangan tidak tersedia pada tanggal dan waktu yang dipilih.');
        }

        Peminjaman::create([
            'id_user' => Auth::id(),
            'id_ruang' => $request->id_ruang,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali' => $request->tanggal_kembali,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'keperluan' => $request->keperluan,
            'status' => 'pending',
        ]);

        return redirect()->route('peminjam.peminjaman.index')
            ->with('success', 'Pengajuan peminjaman berhasil dikirim. Menunggu persetujuan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $peminjaman = Peminjaman::with('ruang')->findOrFail($id);

        if ($peminjaman->id_user !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('peminjam.peminjaman.show', compact('peminjaman'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        abort(404);
    }

    /**
     * Cancel peminjaman
     */
    public function cancel($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        if ($peminjaman->id_user !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($peminjaman->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya peminjaman pending yang dapat dibatalkan.');
        }

        $peminjaman->update(['status' => 'cancelled']);

        return redirect()->route('peminjam.peminjaman.index')
            ->with('success', 'Peminjaman berhasil dibatalkan.');
    }
}

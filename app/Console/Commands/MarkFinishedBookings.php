<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Peminjaman;
use App\Models\Ruang;
use App\Services\RoomRelocationService;
use Carbon\Carbon;

class MarkFinishedBookings extends Command
{
    protected $signature = 'peminjaman:mark-finished';
    protected $description = 'Tandai peminjaman yang sudah selesai dan perbarui status ruang';

    public function handle()
    {
        $now = Carbon::now();
        $relocationService = new RoomRelocationService();

        // 1. Tandai peminjaman approved yang sudah lewat sebagai selesai
        $approved = Peminjaman::where('status', 'approved')->get();
        foreach ($approved as $p) {
            $end = Carbon::parse($p->tanggal_kembali . ' ' . $p->waktu_selesai);
            if ($end->lessThanOrEqualTo($now)) {
                $p->status = 'selesai';
                $p->save();
                
                // Kembalikan pengguna default ke ruangan aslinya
                $returnResult = $relocationService->returnDefaultUser($p);
                if ($returnResult['returned']) {
                    $this->info($returnResult['message']);
                }
            }
        }

        // 2. Perbarui status ruang
        $ruangs = Ruang::all();
        foreach ($ruangs as $ruang) {
            $aktif = Peminjaman::where('id_ruang', $ruang->id_ruang)
                ->where('status', 'approved')
                ->get()
                ->filter(function ($p) use ($now) {
                    $start = Carbon::parse($p->tanggal_pinjam . ' ' . $p->waktu_mulai);
                    $end = Carbon::parse($p->tanggal_kembali . ' ' . $p->waktu_selesai);
                    return $now->between($start, $end);
                });

            // Jika tidak ada peminjaman aktif dan tidak sedang menampung pengguna sementara
            if ($aktif->isEmpty()) {
                // Cek apakah ada pengguna default
                if (!empty($ruang->pengguna_default)) {
                    $ruang->status = 'dipakai'; // Ada pengguna default
                } elseif ($ruang->is_temporary_occupied) {
                    $ruang->status = 'dipakai'; // Menampung temporary
                } else {
                    $ruang->status = 'kosong'; // Benar-benar kosong
                }
            } else {
                $ruang->status = 'dipakai';
            }
            $ruang->save();
        }

        $this->info('Status peminjaman dan ruang diperbarui: ' . $now->toDateTimeString());
    }
}

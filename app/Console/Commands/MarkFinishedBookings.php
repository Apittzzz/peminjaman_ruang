<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Peminjaman;
use App\Models\Ruang;
use Carbon\Carbon;

class MarkFinishedBookings extends Command
{
    protected $signature = 'peminjaman:mark-finished';
    protected $description = 'Tandai peminjaman yang sudah selesai dan perbarui status ruang';

    public function handle()
    {
        $now = Carbon::now();

        // 1. Tandai peminjaman approved yang sudah lewat sebagai selesai
        $approved = Peminjaman::where('status', 'approved')->get();
        foreach ($approved as $p) {
            $end = Carbon::parse($p->tanggal_pinjam . ' ' . $p->waktu_selesai);
            if ($end->lessThanOrEqualTo($now)) {
                $p->status = 'selesai';
                $p->save();
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
                    $end = Carbon::parse($p->tanggal_pinjam . ' ' . $p->waktu_selesai);
                    return $now->between($start, $end);
                });

            $ruang->status = $aktif->isNotEmpty() ? 'dipakai' : 'kosong';
            $ruang->save();
        }

        $this->info('Status peminjaman dan ruang diperbarui: ' . $now->toDateTimeString());
    }
}

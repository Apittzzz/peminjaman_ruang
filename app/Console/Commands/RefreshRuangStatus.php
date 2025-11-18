<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ruang;
use App\Models\Peminjaman;
use Carbon\Carbon;

/**
 * Command untuk refresh status ruangan
 * 
 * Mengecek semua ruangan dan update status berdasarkan:
 * - Ada peminjaman aktif atau tidak
 * - Apakah sedang menampung pengguna temporary atau tidak
 */
class RefreshRuangStatus extends Command
{
    /**
     * Signature command
     *
     * @var string
     */
    protected $signature = 'ruang:refresh-status';

    /**
     * Deskripsi command
     *
     * @var string
     */
    protected $description = 'Refresh status semua ruangan berdasarkan peminjaman aktif';

    /**
     * Execute the console command
     */
    public function handle()
    {
        $now = Carbon::now();
        $this->info('Memulai refresh status ruangan...');
        
        $ruangs = Ruang::all();
        $updated = 0;

        foreach ($ruangs as $ruang) {
            $oldStatus = $ruang->status;
            
            // Cek apakah ada peminjaman yang sedang berlangsung
            $aktivePeminjaman = Peminjaman::where('id_ruang', $ruang->id_ruang)
                ->where('status', 'approved')
                ->get()
                ->filter(function ($peminjaman) use ($now) {
                    $start = Carbon::parse($peminjaman->tanggal_pinjam . ' ' . $peminjaman->jam_mulai);
                    $end = Carbon::parse($peminjaman->tanggal_kembali . ' ' . $peminjaman->jam_selesai);
                    return $now->between($start, $end);
                });

            // Update status berdasarkan kondisi
            if ($aktivePeminjaman->isNotEmpty()) {
                // Ada peminjaman aktif
                $ruang->status = 'dipakai';
            } elseif ($ruang->is_temporary_occupied) {
                // Tidak ada peminjaman tapi sedang menampung pengguna temporary
                $ruang->status = 'dipakai';
            } elseif (!empty($ruang->pengguna_default)) {
                // Tidak ada peminjaman tapi ada pengguna default
                $ruang->status = 'dipakai';
            } else {
                // Tidak ada peminjaman, tidak menampung pengguna temporary, dan tidak ada pengguna default
                $ruang->status = 'kosong';
            }

            // Save jika ada perubahan
            if ($oldStatus !== $ruang->status) {
                $ruang->save();
                $updated++;
                $this->line("  ✓ {$ruang->nama_ruang}: {$oldStatus} → {$ruang->status}");
            }
        }

        if ($updated > 0) {
            $this->info("✅ {$updated} ruangan berhasil diperbarui statusnya.");
        } else {
            $this->info("ℹ️  Tidak ada perubahan status ruangan.");
        }
        
        $this->info("Selesai pada: " . $now->toDateTimeString());
        
        return Command::SUCCESS;
    }
}

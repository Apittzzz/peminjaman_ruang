<?php

namespace App\Services;

use App\Models\Ruang;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RoomRelocationService
{
    /**
     * Pindahkan pengguna default dari ruangan yang akan dipinjam ke ruangan kosong
     * 
     * @param Peminjaman $peminjaman
     * @return array
     */
    public function relocateDefaultUser(Peminjaman $peminjaman)
    {
        try {
            DB::beginTransaction();
            
            $ruangDipinjam = Ruang::find($peminjaman->id_ruang);
            
            // Cek apakah ruangan memiliki pengguna default
            if (empty($ruangDipinjam->pengguna_default)) {
                Log::info("Ruang {$ruangDipinjam->nama_ruang} tidak memiliki pengguna default");
                DB::commit();
                return [
                    'success' => true,
                    'message' => 'Tidak ada pengguna default untuk dipindahkan',
                    'relocated' => false
                ];
            }
            
            // Cari ruangan kosong yang tersedia untuk pengguna default
            $ruangKosong = $this->findAvailableRoom($peminjaman, $ruangDipinjam);
            
            if (!$ruangKosong) {
                Log::warning("Tidak ada ruangan kosong untuk memindahkan pengguna default dari {$ruangDipinjam->nama_ruang}");
                DB::commit();
                return [
                    'success' => false,
                    'message' => 'Tidak ada ruangan kosong tersedia untuk pemindahan pengguna default',
                    'relocated' => false
                ];
            }
            
            // Simpan informasi pengguna default yang akan dipindahkan
            $penggunaDefault = $ruangDipinjam->pengguna_default;
            
            // Update ruangan kosong dengan pengguna default sementara
            $ruangKosong->update([
                'pengguna_default_temp' => $penggunaDefault,
                'is_temporary_occupied' => true,
                'ruang_asal_id' => $ruangDipinjam->id_ruang,
                'keterangan_penggunaan' => "Pengguna sementara dari {$ruangDipinjam->nama_ruang} (ID Peminjaman: {$peminjaman->id_peminjaman})"
            ]);
            
            // Simpan informasi pemindahan di peminjaman
            $peminjaman->update([
                'catatan' => ($peminjaman->catatan ? $peminjaman->catatan . ' | ' : '') . 
                            "Pengguna default '{$penggunaDefault}' dipindah sementara ke {$ruangKosong->nama_ruang}"
            ]);
            
            Log::info("Pengguna default '{$penggunaDefault}' dipindahkan dari {$ruangDipinjam->nama_ruang} ke {$ruangKosong->nama_ruang}");
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => "Pengguna default '{$penggunaDefault}' berhasil dipindahkan ke {$ruangKosong->nama_ruang}",
                'relocated' => true,
                'ruang_asal' => $ruangDipinjam->nama_ruang,
                'ruang_tujuan' => $ruangKosong->nama_ruang,
                'pengguna_default' => $penggunaDefault
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error dalam relocateDefaultUser: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat memindahkan pengguna default: ' . $e->getMessage(),
                'relocated' => false
            ];
        }
    }
    
    /**
     * Kembalikan pengguna default ke ruangan aslinya setelah peminjaman selesai
     * 
     * @param Peminjaman $peminjaman
     * @return array
     */
    public function returnDefaultUser(Peminjaman $peminjaman)
    {
        try {
            DB::beginTransaction();
            
            $ruangDipinjam = Ruang::find($peminjaman->id_ruang);
            
            // Cari ruangan yang menampung pengguna default sementara dari ruangan ini
            $ruangSementara = Ruang::where('ruang_asal_id', $ruangDipinjam->id_ruang)
                ->where('is_temporary_occupied', true)
                ->first();
            
            if (!$ruangSementara) {
                Log::info("Tidak ada pengguna default yang perlu dikembalikan untuk ruang {$ruangDipinjam->nama_ruang}");
                DB::commit();
                return [
                    'success' => true,
                    'message' => 'Tidak ada pengguna default yang perlu dikembalikan',
                    'returned' => false
                ];
            }
            
            $penggunaDefault = $ruangSementara->pengguna_default_temp;
            
            // Kembalikan ruangan sementara ke kondisi semula
            $ruangSementara->update([
                'pengguna_default_temp' => null,
                'is_temporary_occupied' => false,
                'ruang_asal_id' => null,
                'keterangan_penggunaan' => null
            ]);
            
            Log::info("Pengguna default '{$penggunaDefault}' dikembalikan dari {$ruangSementara->nama_ruang} ke {$ruangDipinjam->nama_ruang}");
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => "Pengguna default '{$penggunaDefault}' berhasil dikembalikan ke {$ruangDipinjam->nama_ruang}",
                'returned' => true,
                'ruang_asal' => $ruangDipinjam->nama_ruang,
                'ruang_sementara' => $ruangSementara->nama_ruang,
                'pengguna_default' => $penggunaDefault
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error dalam returnDefaultUser: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengembalikan pengguna default: ' . $e->getMessage(),
                'returned' => false
            ];
        }
    }
    
    /**
     * Cari ruangan kosong yang tersedia untuk menampung pengguna default sementara
     * 
     * @param Peminjaman $peminjaman
     * @param Ruang $ruangDipinjam
     * @return Ruang|null
     */
    private function findAvailableRoom(Peminjaman $peminjaman, Ruang $ruangDipinjam)
    {
        // Cari ruangan yang:
        // 1. Status kosong
        // 2. Tidak memiliki pengguna default
        // 3. Tidak sedang dipinjam pada periode yang sama
        // 4. Tidak sedang menampung pengguna default sementara
        
        $ruangKosong = Ruang::where('status', 'kosong')
            ->whereNull('pengguna_default')
            ->where('is_temporary_occupied', false)
            ->where('id_ruang', '!=', $ruangDipinjam->id_ruang)
            ->whereDoesntHave('peminjaman', function ($query) use ($peminjaman) {
                $query->where('status', '!=', 'cancelled')
                    ->where('status', '!=', 'rejected')
                    ->where(function ($q) use ($peminjaman) {
                        $q->whereBetween('tanggal_pinjam', [$peminjaman->tanggal_pinjam, $peminjaman->tanggal_kembali])
                          ->orWhereBetween('tanggal_kembali', [$peminjaman->tanggal_pinjam, $peminjaman->tanggal_kembali])
                          ->orWhere(function ($subQ) use ($peminjaman) {
                              $subQ->where('tanggal_pinjam', '<=', $peminjaman->tanggal_pinjam)
                                   ->where('tanggal_kembali', '>=', $peminjaman->tanggal_kembali);
                          });
                    });
            })
            ->first();
        
        return $ruangKosong;
    }
}

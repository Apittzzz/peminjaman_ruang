<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Ruang extends Model
{
    use HasFactory;

    protected $table = 'ruang';
    protected $primaryKey = 'id_ruang';
    public $timestamps = true;

    protected $fillable = [
        'nama_ruang',
        'kapasitas',
        'lokasi',
        'fasilitas',
        'status',
        'pengguna_default',
        'keterangan_penggunaan',
        'ruang_asal_id',
        'pengguna_default_temp',
        'is_temporary_occupied',
    ];

    /**
     * Relasi ke peminjaman
     */
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'id_ruang');
    }

    /**
     * Relasi ke user sebagai pengguna default (jika ID)
     * Note: pengguna_default bisa berupa ID user atau text description
     */
    public function penggunaDefault()
    {
        // Always define the relation as a belongsTo so it can be eager-loaded.
        // If `pengguna_default` stores a textual description instead of a user id,
        // the relation will simply return null for that instance.
        // Use the actual users table primary key `id_user` as the owner key.
        return $this->belongsTo(User::class, 'pengguna_default', 'id_user');
    }

    /**
     * Helper accessor to get a display value for pengguna_default.
     * If `pengguna_default` stores a numeric user id, return the related user's name.
     * Otherwise return the raw text stored in the column.
     */
    public function getPenggunaDefaultTextAttribute()
    {
        if (is_numeric($this->pengguna_default)) {
            return $this->penggunaDefault ? ($this->penggunaDefault->nama ?? $this->pengguna_default) : $this->pengguna_default;
        }

        return $this->pengguna_default;
    }

    /**
     * Relasi ke ruang asal (untuk temporary relocation)
     */
    public function ruangAsal()
    {
        return $this->belongsTo(Ruang::class, 'ruang_asal_id', 'id_ruang');
    }

    /**
     * Get status display dengan badge color
     */
    public function getStatusBadgeAttribute()
    {
        return $this->status === 'kosong' 
            ? '<span class="badge bg-success">Kosong</span>' 
            : '<span class="badge bg-danger">Dipakai</span>';
    }

    /**
     * Check if ruangan truly available (no booking, no default user, not temporary occupied)
     */
    public function isTrulyAvailable()
    {
        return $this->status === 'kosong' 
            && empty($this->pengguna_default) 
            && !$this->is_temporary_occupied;
    }
}
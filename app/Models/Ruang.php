<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruang extends Model
{
    use HasFactory;

    protected $table = 'ruang';
    protected $primaryKey = 'id_ruang';
    public $timestamps = true;

    protected $fillable = [
        'nama_ruang',
        'kapasitas',
        'status',
    ];

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'id_ruang');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporan';
    protected $primaryKey = 'id_laporan';
    public $timestamps = true;

    protected $fillable = [
        'id_user',
        'tanggal_laporan',
        'jenis_laporan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
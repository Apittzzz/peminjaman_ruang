<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_ADMIN = 'admin';
    const ROLE_PETUGAS = 'petugas';
    const ROLE_PEMINJAM = 'peminjam';

    protected $table = 'users';
    protected $primaryKey = 'id_user';
    public $timestamps = true;

    protected $fillable = [
        'username',
        'password',
        'nama',
        'name',
        'email',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Find the user instance for the given username.
     */
    public function findForPassport(string $username): User
    {
        return $this->where('username', $username)->first();
    }

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'id_user');
    }

    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'id_user');
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isPetugas()
    {
        return $this->role === self::ROLE_PETUGAS;
    }

    public function isPeminjam()
    {
        return $this->role === self::ROLE_PEMINJAM;
    }
}
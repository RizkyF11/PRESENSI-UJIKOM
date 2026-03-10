<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';

    protected $fillable = [
        'user_id',
        'nip',
        'jabatan',
        'no_hp',
        'alamat',
        'status',
    ];

    // Karyawan milik 1 User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Karyawan punya banyak Absensi
    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    // Karyawan punya banyak Izin
    public function izin()
    {
        return $this->hasMany(Izin::class);
    }

    // Karyawan punya banyak Cuti
    public function cuti()
    {
        return $this->hasMany(Cuti::class);
    }

    // Many-to-Many ke Shift
    public function shifts()
    {
        return $this->belongsToMany(
            Shift::class,
            'karyawan_shift',
        )->withPivot('tanggal_mulai', 'tanggal_selesai');
    }
}

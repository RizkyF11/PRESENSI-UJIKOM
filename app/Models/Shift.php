<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $table = 'shift';
    protected $fillable = [
        'nama_shift',
        'jam_masuk',
        'jam_keluar',
        'toleransi_menit',
    ];

    //shift dipakai banyak karyawan
    public function karyawan()
    {
        return $this->belongsToMany(
            Karyawan::class,
            'karyawan_shift'
        )->withPivot('tanggal_mulai', 'tanggal_selesai');
    }

    // Shift punya banyak absensi
    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }
    
    public function izin()
    {
        return $this->hasMany(Izin::class);
    }

    public function cuti()
    {
        return $this->hasMany(Cuti::class);
    }
}

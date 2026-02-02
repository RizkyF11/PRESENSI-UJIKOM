<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Absensi extends Model
{
    use HasFactory;
    protected $table = 'absensi ';
    protected $fillable = [
        'karyawan_id',
        'shift_id',
        'qr_code_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'latitude',
        'longitude',
        'status',
    ];

    // Absensi milik 1 karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    // Absensi milik 1 shift
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    // Absensi pakai 1 QR
    public function qrCode()
    {
        return $this->belongsTo(QrCode::class);
    }

    // Absensi bisa punya 1 izin
    public function izin()
    {
        return $this->hasOne(Izin::class);
    }
}

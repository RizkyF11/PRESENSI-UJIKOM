<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Absensi extends Model
{
    use HasFactory;
    protected $table = 'absensi'; //property
    protected $fillable = [         //property
        'lokasi_kantor_id',
        'karyawan_id',
        'shift_id',
        'qr_code_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'latitude_masuk',
        'longitude_masuk',
        'latitude_keluar',
        'longitude_keluar',
        'status_masuk',
        'status_keluar',
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


    public function lokasiKantor()
    {
        return $this->belongsTo(LokasiKantor::class, 'lokasi_kantor_id');
    }

    /*
    |--------------------------------------------------------------------------
    | RELATION TO USER TOKEN
    |--------------------------------------------------------------------------
    | 1 Absensi bisa menggunakan 1 token fleksibilitas
    | Nullable karena tidak semua absensi pakai token
    |
    */
    public function userToken()
    {
        return $this->hasOne(UserToken::class, 'used_at_absensi_id');
    }
}

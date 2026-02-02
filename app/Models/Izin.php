<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Izin extends Model
{
    use HasFactory;

    protected $table = 'izin';

    protected $fillable = [
        'karyawan_id',
        'absensi_id',
        'tanggal',
        'alasan',
        'status',
    ];

    // Izin milik 1 karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    // Izin terhubung ke 1 absensi
    public function absensi()
    {
        return $this->belongsTo(Absensi::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KaryawanShift extends Model
{
    protected $table = 'karyawan_shift';

    protected $fillable = [
        'karyawan_id',
        'shift_id',
        'tanggal_mulai',
        'tanggal_selesai',
    ];
}

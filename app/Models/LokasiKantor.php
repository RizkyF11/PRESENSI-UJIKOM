<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LokasiKantor extends Model
{
    protected $table = 'lokasi_kantor';

    protected $fillable = [
        'nama_lokasi',
        'latitude',
        'longitude',
        'radius',
        'is_active',
    ];

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'lokasi_kantor_id');
    }
}

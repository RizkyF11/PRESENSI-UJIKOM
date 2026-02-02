<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    use HasFactory;

    protected $table = 'qr_code';

    protected $fillable = [
        'kode',
        'is_active',
        'expired_at',
    ];

      // QR dipakai di banyak absensi
    public function absensi()
    {  
        return $this->hasMany(Absensi::class);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QrCode;
use Illuminate\Support\Str;

class QrCodeSeeder extends Seeder
{
    public function run(): void
    {
        // nonaktifkan semua QR lama
        QrCode::query()->update(['is_active' => false]);

        // buat QR aktif
        QrCode::create([
            'kode' => Str::uuid(),
            'is_active' => true,
            'expired_at' => now()->addMinutes(5),
        ]);
    }
}

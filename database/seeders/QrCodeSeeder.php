<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\QrCode;

class QrCodeSeeder extends Seeder
{
    public function run(): void
    {
        // Nonaktifkan semua QR lama
        QrCode::query()->update(['is_active' => false]);

        // Buat satu QR aktif baru
        QrCode::create([
            'kode'       => Str::uuid(),
            'is_active'  => true,
            'expired_at' => now()->addMinutes(5),
        ]);
    }
}
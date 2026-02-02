<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{
    User,
    Karyawan,
    Shift,
    QrCode,
    Absensi,
    Izin,
    Cuti
};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'nama' => 'Admin',
            'email' => 'admin@mail.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Shift
        $shift = Shift::factory()->count(2)->create();

        // QR Code
        QrCode::factory()->count(3)->create();

        // Karyawan + User
        Karyawan::factory()
            ->count(5)
            ->create()
            ->each(function ($karyawan) use ($shift) {

                // attach shift
                $karyawan->shifts()->attach(
                    $shift->random()->id,
                    [
                        'tanggal_mulai' => now()->subMonth(),
                        'tanggal_selesai' => null,
                    ]
                );

                // absensi
                $absensi = Absensi::factory()->create([
                    'karyawan_id' => $karyawan->id,
                    'shift_id' => $shift->random()->id,
                ]);

                // izin contoh
                if (rand(0, 1)) {
                    Izin::factory()->create([
                        'karyawan_id' => $karyawan->id,
                        'absensi_id' => $absensi->id,
                    ]);
                }

                // cuti contoh
                if (rand(0, 1)) {
                    Cuti::factory()->create([
                        'karyawan_id' => $karyawan->id,
                    ]);
                }
            });
    }
}

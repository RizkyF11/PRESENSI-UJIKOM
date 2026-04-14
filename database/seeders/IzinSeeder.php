<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Izin;
use App\Models\Karyawan;

class IzinSeeder extends Seeder
{
    public function run(): void
    {
        $alasans = [
            'Sakit',
            'Acara Keluarga',
            'Keperluan Pribadi'
        ];

        foreach (Karyawan::all() as $karyawan) {

            if (rand(1, 100) > 70) continue;

            $jumlahIzin = rand(1, 3);

            for ($i = 0; $i < $jumlahIzin; $i++) {

                $tanggal = Carbon::today()->subDays(rand(5, 90));

                if ($tanggal->isWeekend()) continue;

                Izin::create([
                    'karyawan_id' => $karyawan->id,
                    'tanggal_mulai' => $tanggal,
                    'tanggal_selesai' => $tanggal->copy()->addDays(rand(0, 2)),
                    'alasan' => $alasans[array_rand($alasans)],
                    'status' => 'approved'
                ]);
            }
        }
    }
}
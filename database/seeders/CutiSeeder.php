<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Cuti;
use App\Models\Karyawan;

class CutiSeeder extends Seeder
{
    public function run(): void
    {
        $alasans = [
            'Cuti Tahunan',
            'Liburan',
            'Keperluan Keluarga'
        ];

        foreach (Karyawan::all() as $karyawan) {

            if (rand(1, 100) > 60) continue;

            $jumlahCuti = rand(1, 2);

            for ($i = 0; $i < $jumlahCuti; $i++) {

                $tanggal = Carbon::today()->subDays(rand(10, 120));

                if ($tanggal->isWeekend()) continue;

                Cuti::create([
                    'karyawan_id' => $karyawan->id,
                    'tanggal_mulai' => $tanggal,
                    'tanggal_selesai' => $tanggal->copy()->addDays(rand(2, 5)),
                    'alasan' => $alasans[array_rand($alasans)],
                    'status' => 'approved'
                ]);
            }
        }
    }
}
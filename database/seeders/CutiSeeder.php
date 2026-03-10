<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\{Cuti, Karyawan};

class CutiSeeder extends Seeder
{
    public function run(): void
    {
        $alasans = [
            'Cuti Tahunan',
            'Cuti Melahirkan',
            'Cuti Sakit',
            'Keperluan Keluarga',
            'Liburan Keluarga',
        ];

        $karyawans = Karyawan::all();

        // Sebagian karyawan cuti HARI INI (approved) - untuk dashboard
        $karyawanHariIni = $karyawans->skip(3)->take(2); // 2 karyawan cuti hari ini (skip 3 yang sudah izin)

        foreach ($karyawanHariIni as $karyawan) {
            $mulai = Carbon::today();

            Cuti::create([
                'karyawan_id'     => $karyawan->id,
                'tanggal_mulai'   => $mulai->toDateString(),
                'tanggal_selesai' => (clone $mulai)->addDays(rand(2, 5))->toDateString(),
                'alasan'          => $alasans[array_rand($alasans)],
                'status'          => 'approved',
            ]);
        }

        // Sisanya cuti dengan tanggal historis (mix status)
        $karyawanHistoris = $karyawans->skip(5);

        foreach ($karyawanHistoris as $karyawan) {
            // 60% chance punya cuti historis
            if (rand(1, 10) > 6) continue;

            $jumlah = rand(1, 2);

            for ($i = 0; $i < $jumlah; $i++) {
                // Tanggal historis 14-90 hari lalu (sudah lewat)
                $mulai = Carbon::today()->subDays(rand(14, 90));

                Cuti::create([
                    'karyawan_id'     => $karyawan->id,
                    'tanggal_mulai'   => $mulai->toDateString(),
                    'tanggal_selesai' => (clone $mulai)->addDays(rand(2, 7))->toDateString(),
                    'alasan'          => $alasans[array_rand($alasans)],
                    'status'          => fake()->randomElement(['approved', 'reject']),
                ]);
            }
        }

        // Tambah beberapa pengajuan cuti pending untuk dashboard "Pending Pengajuan"
        foreach ($karyawans->skip(5)->take(3) as $karyawan) {
            $mulai = Carbon::today()->addDays(rand(3, 14)); // cuti mulai beberapa hari ke depan

            Cuti::create([
                'karyawan_id'     => $karyawan->id,
                'tanggal_mulai'   => $mulai->toDateString(),
                'tanggal_selesai' => (clone $mulai)->addDays(rand(2, 7))->toDateString(),
                'alasan'          => $alasans[array_rand($alasans)],
                'status'          => 'pending',
            ]);
        }
    }
}
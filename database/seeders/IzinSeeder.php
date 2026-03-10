<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\{Izin, Karyawan};

class IzinSeeder extends Seeder
{
    public function run(): void
    {
        $alasans = [
            'Sakit',
            'Keperluan Keluarga',
            'Urusan Pribadi',
            'Pemeriksaan Dokter',
            'Acara Keluarga',
        ];

        $karyawans = Karyawan::all();

        // Sebagian karyawan izin HARI INI (approved) - untuk dashboard
        $karyawanHariIni = $karyawans->take(3); // 3 karyawan izin hari ini

        foreach ($karyawanHariIni as $karyawan) {
            $mulai = Carbon::today();

            Izin::create([
                'karyawan_id'     => $karyawan->id,
                'tanggal_mulai'   => $mulai->toDateString(),
                'tanggal_selesai' => (clone $mulai)->addDays(rand(1, 2))->toDateString(),
                'alasan'          => $alasans[array_rand($alasans)],
                'status'          => 'approved',
            ]);
        }

        // Sisanya izin dengan tanggal historis (mix status)
        $karyawanHistoris = $karyawans->skip(3);

        foreach ($karyawanHistoris as $karyawan) {
            // Tidak semua karyawan punya izin historis - 70% chance
            if (rand(1, 10) > 7) continue;

            $jumlah = rand(1, 2);

            for ($i = 0; $i < $jumlah; $i++) {
                // Tanggal historis 7-60 hari lalu (sudah lewat)
                $mulai = Carbon::today()->subDays(rand(7, 60));

                Izin::create([
                    'karyawan_id'     => $karyawan->id,
                    'tanggal_mulai'   => $mulai->toDateString(),
                    'tanggal_selesai' => (clone $mulai)->addDays(rand(1, 3))->toDateString(),
                    'alasan'          => $alasans[array_rand($alasans)],
                    'status'          => fake()->randomElement(['approved', 'reject']),
                ]);
            }
        }

        // Tambah beberapa pengajuan pending untuk dashboard "Pending Pengajuan"
        foreach ($karyawans->take(4) as $karyawan) {
            $mulai = Carbon::today()->addDays(rand(1, 7)); // izin mulai besok/minggu depan

            Izin::create([
                'karyawan_id'     => $karyawan->id,
                'tanggal_mulai'   => $mulai->toDateString(),
                'tanggal_selesai' => (clone $mulai)->addDays(rand(1, 3))->toDateString(),
                'alasan'          => $alasans[array_rand($alasans)],
                'status'          => 'pending',
            ]);
        }
    }
}
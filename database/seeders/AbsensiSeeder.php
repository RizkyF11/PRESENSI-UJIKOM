<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\{Karyawan, Shift, Absensi, LokasiKantor};

class AbsensiSeeder extends Seeder
{
    public function run(): void
    {
        $shift  = Shift::where('nama_shift', 'Pagi')->first();
        $lokasi = LokasiKantor::first();

        if (! $shift || ! $lokasi) {
            $this->command->warn('Shift atau Lokasi Kantor belum ada. Jalankan ShiftSeeder & LokasiKantorSeeder dulu.');
            return;
        }

        $baseLat = $lokasi->latitude;
        $baseLng = $lokasi->longitude;

        // Generate data 1 tahun terakhir untuk setiap karyawan
        $startDate = Carbon::today()->subYear();
        $endDate   = Carbon::today();

        Karyawan::all()->each(function ($karyawan) use ($shift, $lokasi, $baseLat, $baseLng, $startDate, $endDate) {
            $currentDate = clone $startDate;

            while ($currentDate <= $endDate) {
                // Lewati weekend
                if ($currentDate->isWeekend()) {
                    $currentDate->addDay();
                    continue;
                }

                // Hindari duplikat
                $sudahAda = Absensi::where('karyawan_id', $karyawan->id)
                    ->whereDate('tanggal', $currentDate)
                    ->exists();

                if ($sudahAda) {
                    $currentDate->addDay();
                    continue;
                }

                $rand = rand(1, 100);

                // 5% Alpha - tidak insert
                if ($rand <= 5) {
                    $currentDate->addDay();
                    continue;
                }

                // 10% Terlambat, 85% Hadir
                $isLate      = ($rand > 5 && $rand <= 15);
                $masukHour   = $isLate ? 8 : 7;
                $masukMinute = $isLate ? rand(16, 59) : rand(30, 59);

                Absensi::create([
                    'karyawan_id'      => $karyawan->id,
                    'shift_id'         => $shift->id,
                    'lokasi_kantor_id' => $lokasi->id,
                    'qr_code_id'       => null,
                    'tanggal'          => $currentDate->toDateString(),
                    'jam_masuk'        => sprintf('%02d:%02d:00', $masukHour, $masukMinute),
                    'jam_keluar'       => sprintf('17:%02d:00', rand(0, 30)),
                    'latitude_masuk'   => $baseLat + (rand(-100, 100) / 1000000),
                    'longitude_masuk'  => $baseLng + (rand(-100, 100) / 1000000),
                    'latitude_keluar'  => $baseLat + (rand(-100, 100) / 1000000),
                    'longitude_keluar' => $baseLng + (rand(-100, 100) / 1000000),
                    'status_masuk'     => $isLate ? 'terlambat' : 'hadir',
                    'status_keluar'    => 'pulang',
                ]);

                $currentDate->addDay();
            }
        });
    }
}
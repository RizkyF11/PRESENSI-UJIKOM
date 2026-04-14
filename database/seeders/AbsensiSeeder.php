<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\Izin;
use App\Models\Karyawan;
use App\Models\LokasiKantor;
use App\Models\Shift;

class AbsensiSeeder extends Seeder
{
    public function run(): void
    {
        $shift = Shift::where('nama_shift', 'Pagi')->first();
        $lokasi = LokasiKantor::first();

        if (!$shift || !$lokasi) return;

        $startDate = Carbon::today()->subYear();
        $endDate = Carbon::today();

        foreach (Karyawan::all() as $karyawan) {

            $currentDate = $startDate->copy();

            while ($currentDate <= $endDate) {

                if ($currentDate->isWeekend()) {
                    $currentDate->addDay();
                    continue;
                }

                $adaIzin = Izin::where('karyawan_id', $karyawan->id)
                    ->where('status', 'approved')
                    ->whereDate('tanggal_mulai', '<=', $currentDate)
                    ->whereDate('tanggal_selesai', '>=', $currentDate)
                    ->exists();

                $adaCuti = Cuti::where('karyawan_id', $karyawan->id)
                    ->where('status', 'approved')
                    ->whereDate('tanggal_mulai', '<=', $currentDate)
                    ->whereDate('tanggal_selesai', '>=', $currentDate)
                    ->exists();

                if ($adaIzin || $adaCuti) {
                    $currentDate->addDay();
                    continue;
                }

                $rand = rand(1, 100);

                if ($rand <= 10) {
                    $currentDate->addDay();
                    continue;
                }

                $isLate = $rand <= 25;

                Absensi::create([
                    'karyawan_id' => $karyawan->id,
                    'shift_id' => $shift->id,
                    'lokasi_kantor_id' => $lokasi->id,
                    'tanggal' => $currentDate,

                    'jam_masuk' => $isLate
                        ? '08:' . rand(10, 59) . ':00'
                        : '07:' . rand(0, 59) . ':00',

                    'jam_keluar' => '17:' . rand(0, 59) . ':00',

                    'latitude_masuk' => $lokasi->latitude,
                    'longitude_masuk' => $lokasi->longitude,
                    'latitude_keluar' => $lokasi->latitude,
                    'longitude_keluar' => $lokasi->longitude,

                    'status_masuk' => $isLate ? 'terlambat' : 'hadir',
                    'status_keluar' => 'pulang',
                ]);

                $currentDate->addDay();
            }
        }
    }
}
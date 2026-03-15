<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\Izin;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Console\Command;
class GenerateAlpha extends Command
{

    protected $signature = 'absensi:generate-alpha';
    protected $description = 'Generate alpha otomatis berdasarkan dengan shift aktif';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        $karyawanList = Karyawan::whereHas('shifts', function ($q) use ($now) {
            $q->wherePivot('tanggal_mulai', '<=', $now->toDateString())
                ->where(function ($query) use ($now) {
                    $query->wherePivot('tanggal_selesai', '>=', $now->toDateString())
                        ->orWhereNull('tanggal_selesai');
                });
        })
            ->whereDoesntHave('absensi', function ($q) use ($now) {
                $q->whereDate('tanggal', $now->toDateString());
            })
            ->with('shifts')
            ->get();

        foreach ($karyawanList as $karyawan) {

            $shift = $karyawan->shifts
                ->filter(function ($shift) use ($now) {

                    $mulai = Carbon::parse($shift->pivot->tanggal_mulai);
                    $selesai = $shift->pivot->tanggal_selesai
                        ? Carbon::parse($shift->pivot->tanggal_selesai)
                        : null;

                    if ($selesai) {
                        return $now->between($mulai, $selesai);
                    }

                    return $now->gte($mulai);
                })
                ->first();

            if (!$shift) {
                continue;
            }

            $jamMasuk  = Carbon::parse($shift->jam_masuk);
            $jamKeluar = Carbon::parse($shift->jam_keluar);

            // ==========================
            // DETEKSI SHIFT LINTAS HARI
            // ==========================
            $isShiftMalam = $jamKeluar->lessThan($jamMasuk);

            //tentukan tanggal kerja
            $tanggalKerja = $now->copy()->startOfDay();

            if ($isShiftMalam) {
                //jika sekarang setelah midnight tapi sebelum jam keluar
                if ($now->format('H:i:s') <= $shift->jam_keluar) {
                    $tanggalKerja = $now->copy()->subDay()->startOfDay();
                }
            }

            // buat datetime batas alpha
            $batasAlpha = Carbon::parse(
                $tanggalKerja->format('Y-m-d') . ' ' . $shift->jam_keluar
            );

            if ($isShiftMalam) {
                $batasAlpha->addDay();
            }

            // Kalau belum lewat batas → skip
            if ($now->lessThan($batasAlpha)) {
                continue;
            }

            // ==========================
            // CEK SUDAH ADA ABSENSI?
            // ==========================
            $sudahAbsen = Absensi::where('karyawan_id', $karyawan->id)
                ->whereDate('tanggal', $tanggalKerja->toDateString())
                ->exists();

            if ($sudahAbsen) {
                continue;
            }

            // ==========================
            // CEK IZIN
            // ==========================
            $izin = Izin::where('karyawan_id', $karyawan->id)
                ->where('status', 'approved')
                ->whereDate('tanggal_mulai', '<=', $tanggalKerja)
                ->whereDate('tanggal_selesai', '>=', $tanggalKerja)
                ->exists();

            if ($izin) {
                continue;
            }

            // ==========================
            // CEK CUTI
            // ==========================
            $cuti = Cuti::where('karyawan_id', $karyawan->id)
                ->where('status', 'approved')
                ->whereDate('tanggal_mulai', '<=', $tanggalKerja)
                ->whereDate('tanggal_selesai', '>=', $tanggalKerja)
                ->exists();

            if ($cuti) {
                continue;
            }

            // ==========================
            // INSERT ALPHA
            // ==========================
            Absensi::create([
                'karyawan_id' => $karyawan->id,
                'shift_id'    => $shift->id,
                'tanggal'     => $tanggalKerja,
            ]);
        }

        $this->info('Generate alpha berhasil dijalankan.');
    }
}

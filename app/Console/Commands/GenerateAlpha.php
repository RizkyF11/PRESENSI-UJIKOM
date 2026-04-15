<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\Izin;
use App\Models\Karyawan;
use App\Models\Shift;
use App\Services\GamificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateAlpha extends Command
{
    protected $signature = 'absensi:generate-alpha';
    protected $description = 'Generate alpha otomatis berdasarkan shift aktif';

    public function handle()
    {
        $now = Carbon::now();

        $karyawanList = Karyawan::with(['shifts'])->get();

        foreach ($karyawanList as $karyawan) {

            foreach ($karyawan->shifts as $shift) {

                // validasi masa shift aktif
                $mulai = Carbon::parse($shift->pivot->tanggal_mulai);

                $selesai = $shift->pivot->tanggal_selesai
                    ? Carbon::parse($shift->pivot->tanggal_selesai)
                    : null;

                if ($selesai && !$now->between($mulai, $selesai)) {
                    continue;
                }

                if (!$selesai && $now->lt($mulai)) {
                    continue;
                }

                // ==========================
                // TENTUKAN TANGGAL KERJA
                // ==========================
                $jamMasuk  = Carbon::parse($shift->jam_masuk);
                $jamKeluar = Carbon::parse($shift->jam_keluar);

                $isNightShift = $jamKeluar->lessThan($jamMasuk);

                $tanggalKerja = $now->copy()->startOfDay();
                if ($isNightShift) {

                    $jamKeluarBatas = Carbon::parse($shift->jam_keluar);

                    // kalau waktu sekarang masih sebelum jam keluar shift malam
                    if ($now->lt($jamKeluarBatas)) {
                        $tanggalKerja = $now->copy()->subDay()->startOfDay();
                    }
                }

                // skip weekend
                if ($tanggalKerja->isWeekend()) {
                    continue;
                }

                // ==========================
                // CEK SUDAH ABSEN
                // ==========================
                $sudahAbsen = Absensi::where('karyawan_id', $karyawan->id)
                    ->where('shift_id', $shift->id)
                    ->whereDate('tanggal', $tanggalKerja)
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

                $existing = Absensi::where('karyawan_id', $karyawan->id)
                    ->where('shift_id', $shift->id)
                    ->whereDate('tanggal', $tanggalKerja)
                    ->first();

                if ($existing) {
                    continue;
                }

                // ==========================
                // INSERT ALPHA
                // ==========================
                $absensi = Absensi::create([
                    'karyawan_id'   => $karyawan->id,
                    'shift_id'      => $shift->id,
                    'tanggal'       => $tanggalKerja,
                    'jam_masuk'     => null,
                    'jam_keluar'    => null,
                    'status_masuk'  => 'alpha',
                    'is_alpha_generated' => true,
                ]);

                // ==========================
                // TRIGGER GAMIFICATION
                // ==========================
                app(GamificationService::class)
                    ->evaluateAlpha($absensi);
            }
        }

        $this->info('Generate alpha berhasil dijalankan.');
    }
}

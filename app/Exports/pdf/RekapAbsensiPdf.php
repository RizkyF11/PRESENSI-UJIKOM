<?php

namespace App\Exports\Pdf;

use App\Models\Absensi;
use App\Models\Izin;
use App\Models\Cuti;
use App\Models\Karyawan;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class RekapAbsensiPdf
{
    protected $tanggalMulai;
    protected $tanggalSelesai;
    protected $karyawanId;

    public function __construct($tanggalMulai, $tanggalSelesai, $karyawanId = null)
    {
        $this->tanggalMulai = $tanggalMulai;
        $this->tanggalSelesai = $tanggalSelesai;
        $this->karyawanId = $karyawanId;
    }

    public function getData()
    {
        $karyawanQuery = Karyawan::with(['user', 'shifts']);

        if ($this->karyawanId) {
            $karyawanQuery->where('id', $this->karyawanId);
        }

        $karyawanList = $karyawanQuery->get();

        $absensiList = Absensi::whereBetween('tanggal', [
            $this->tanggalMulai,
            $this->tanggalSelesai
        ])->get();

        $izinList = Izin::where('status', 'approved')->get();

        $cutiList = Cuti::where('status', 'approved')->get();

        $data = [];

        foreach ($karyawanList as $karyawan) {

            $periode = CarbonPeriod::create(
                $this->tanggalMulai,
                $this->tanggalSelesai
            );

            $hariKerja = 0;
            $hadir = 0;
            $terlambat = 0;
            $izin = 0;
            $cuti = 0;
            $alpha = 0;

            foreach ($periode as $tanggal) {

                if ($tanggal->isWeekend()) continue;

                $punyaShift = $karyawan->shifts
                    ->filter(function ($shift) use ($tanggal) {

                        $mulai = Carbon::parse($shift->pivot->tanggal_mulai);
                        $selesai = $shift->pivot->tanggal_selesai
                            ? Carbon::parse($shift->pivot->tanggal_selesai)
                            : null;

                        if ($selesai) {
                            return $tanggal->between($mulai, $selesai);
                        }

                        return $tanggal->gte($mulai);
                    })->first();

                if (!$punyaShift) continue;

                $hariKerja++;

                $tanggalStr = $tanggal->format('Y-m-d');

                $adaIzin = $izinList
                    ->where('karyawan_id', $karyawan->id)
                    ->filter(fn($i) =>
                        $tanggalStr >= $i->tanggal_mulai &&
                        $tanggalStr <= $i->tanggal_selesai
                    )->isNotEmpty();

                if ($adaIzin) {
                    $izin++;
                    continue;
                }

                $adaCuti = $cutiList
                    ->where('karyawan_id', $karyawan->id)
                    ->filter(fn($c) =>
                        $tanggalStr >= $c->tanggal_mulai &&
                        $tanggalStr <= $c->tanggal_selesai
                    )->isNotEmpty();

                if ($adaCuti) {
                    $cuti++;
                    continue;
                }

                $absen = $absensiList
                    ->where('karyawan_id', $karyawan->id)
                    ->where('tanggal', $tanggalStr)
                    ->first();

                if ($absen && $absen->jam_masuk) {

                    $hadir++;

                    if ($absen->status_masuk == 'terlambat') {
                        $terlambat++;
                    }

                    continue;
                }

                $alpha++;
            }

            $data[] = [
                'nama' => $karyawan->user->nama ?? '-',
                'hari_kerja' => $hariKerja,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'izin' => $izin,
                'cuti' => $cuti,
                'alpha' => $alpha,
            ];
        }

        return $data;
    }
}
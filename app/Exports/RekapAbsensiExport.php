<?php

namespace App\Exports;

use App\Models\Absensi;
use App\Models\Izin;
use App\Models\Cuti;
use App\Models\Karyawan;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RekapAbsensiExport implements FromCollection, WithHeadings
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

    public function collection()
    {
        $karyawan = Karyawan::with('user');

        if ($this->karyawanId) {
            $karyawan->where('id', $this->karyawanId);
        }

        $karyawan = $karyawan->get();

        $data = [];

        $totalHari = Carbon::parse($this->tanggalMulai)
            ->diffInDays(Carbon::parse($this->tanggalSelesai)) + 1;

        foreach ($karyawan as $k) {

            // ======================
            // ABSENSI
            // ======================

            $absensi = Absensi::where('karyawan_id', $k->id)
                ->whereBetween('tanggal', [$this->tanggalMulai, $this->tanggalSelesai])
                ->get();

            $hadir = $absensi->whereNotNull('jam_masuk')->count();

            $terlambat = $absensi->where('status_masuk', 'terlambat')->count();


            // ======================
            // IZIN (hitung per hari)
            // ======================

            $izinData = Izin::where('karyawan_id', $k->id)
                ->where('status', 'approved')
                ->where(function ($q) {
                    $q->whereBetween('tanggal_mulai', [$this->tanggalMulai, $this->tanggalSelesai])
                      ->orWhereBetween('tanggal_selesai', [$this->tanggalMulai, $this->tanggalSelesai])
                      ->orWhere(function ($q2) {
                          $q2->where('tanggal_mulai', '<=', $this->tanggalMulai)
                             ->where('tanggal_selesai', '>=', $this->tanggalSelesai);
                      });
                })
                ->get();

            $izin = 0;

            foreach ($izinData as $i) {

                $mulai = Carbon::parse($i->tanggal_mulai);
                $selesai = Carbon::parse($i->tanggal_selesai);

                $periode = CarbonPeriod::create($mulai, $selesai);

                foreach ($periode as $tgl) {

                    if ($tgl->between($this->tanggalMulai, $this->tanggalSelesai)) {
                        $izin++;
                    }

                }

            }


            // ======================
            // CUTI (hitung per hari)
            // ======================

            $cutiData = Cuti::where('karyawan_id', $k->id)
                ->where('status', 'approved')
                ->where(function ($q) {
                    $q->whereBetween('tanggal_mulai', [$this->tanggalMulai, $this->tanggalSelesai])
                      ->orWhereBetween('tanggal_selesai', [$this->tanggalMulai, $this->tanggalSelesai])
                      ->orWhere(function ($q2) {
                          $q2->where('tanggal_mulai', '<=', $this->tanggalMulai)
                             ->where('tanggal_selesai', '>=', $this->tanggalSelesai);
                      });
                })
                ->get();

            $cuti = 0;

            foreach ($cutiData as $c) {

                $mulai = Carbon::parse($c->tanggal_mulai);
                $selesai = Carbon::parse($c->tanggal_selesai);

                $periode = CarbonPeriod::create($mulai, $selesai);

                foreach ($periode as $tgl) {

                    if ($tgl->between($this->tanggalMulai, $this->tanggalSelesai)) {
                        $cuti++;
                    }

                }

            }


            // ======================
            // ALPHA
            // ======================

            $alpha = $totalHari - ($hadir + $izin + $cuti);

            if ($alpha < 0) {
                $alpha = 0;
            }


            $data[] = [
                'nama' => $k->user->nama ?? '-',
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'izin' => $izin,
                'cuti' => $cuti,
                'alpha' => $alpha,
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Nama Karyawan',
            'Total Hadir',
            'Total Terlambat',
            'Total Izin',
            'Total Cuti',
            'Total Alpha'
        ];
    }
}
<?php

namespace App\Exports;

use App\Models\Absensi;
use App\Models\Izin;
use App\Models\Cuti;
use App\Models\Karyawan;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class RekapAbsensiSheet implements
    FromCollection,
    WithHeadings,
    WithTitle,
    WithStyles,
    ShouldAutoSize,
    WithEvents
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
        $karyawanQuery = Karyawan::with(['user', 'shifts']);

        if ($this->karyawanId) {
            $karyawanQuery->where('id', $this->karyawanId);
        }

        $karyawanList = $karyawanQuery->get();

        /*
        |--------------------------------------------------------------------------
        | PRELOAD DATA SEKALI SAJA (OPTIMASI QUERY)
        |--------------------------------------------------------------------------
        */

        $absensiList = Absensi::whereBetween('tanggal', [
            $this->tanggalMulai,
            $this->tanggalSelesai
        ])->get();

        $izinList = Izin::where('status', 'approved')
            ->where(function ($q) {
                $q->whereBetween('tanggal_mulai', [$this->tanggalMulai, $this->tanggalSelesai])
                    ->orWhereBetween('tanggal_selesai', [$this->tanggalMulai, $this->tanggalSelesai])
                    ->orWhere(function ($q2) {
                        $q2->where('tanggal_mulai', '<=', $this->tanggalMulai)
                            ->where('tanggal_selesai', '>=', $this->tanggalSelesai);
                    });
            })->get();

        $cutiList = Cuti::where('status', 'approved')
            ->where(function ($q) {
                $q->whereBetween('tanggal_mulai', [$this->tanggalMulai, $this->tanggalSelesai])
                    ->orWhereBetween('tanggal_selesai', [$this->tanggalMulai, $this->tanggalSelesai])
                    ->orWhere(function ($q2) {
                        $q2->where('tanggal_mulai', '<=', $this->tanggalMulai)
                            ->where('tanggal_selesai', '>=', $this->tanggalSelesai);
                    });
            })->get();

        $data = [];

        foreach ($karyawanList as $karyawan) {

            $periode = CarbonPeriod::create(
                Carbon::parse($this->tanggalMulai),
                Carbon::parse($this->tanggalSelesai)
            );

            $hariKerja = 0;
            $hadir = 0;
            $terlambat = 0;
            $izin = 0;
            $cuti = 0;
            $alpha = 0;

            foreach ($periode as $tanggal) {

                /*
                |--------------------------------------------------------------------------
                | SKIP WEEKEND
                |--------------------------------------------------------------------------
                */
                if ($tanggal->isWeekend()) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | CEK ADA SHIFT ATAU TIDAK
                |--------------------------------------------------------------------------
                */
                $punyaShift = $karyawan->shifts
                    ->filter(function ($shift) use ($tanggal) {

                        $mulai = Carbon::parse($shift->pivot->tanggal_mulai)->startOfDay();

                        $selesai = $shift->pivot->tanggal_selesai
                            ? Carbon::parse($shift->pivot->tanggal_selesai)->endOfDay()
                            : null;

                        $hariIni = $tanggal->copy()->startOfDay();

                        if ($selesai) {
                            return $hariIni->between($mulai, $selesai);
                        }

                        return $hariIni->gte($mulai);
                    })
                    ->first();

                if (!$punyaShift) {
                    continue;
                }

                $hariKerja++;

                $tanggalStr = $tanggal->format('Y-m-d');

                /*
                |--------------------------------------------------------------------------
                | CEK IZIN
                |--------------------------------------------------------------------------
                */
                $adaIzin = $izinList
                    ->where('karyawan_id', $karyawan->id)
                    ->filter(function ($item) use ($tanggalStr) {

                        return $tanggalStr >= $item->tanggal_mulai
                            && $tanggalStr <= $item->tanggal_selesai;
                    })
                    ->isNotEmpty();

                if ($adaIzin) {
                    $izin++;
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | CEK CUTI
                |--------------------------------------------------------------------------
                */
                $adaCuti = $cutiList
                    ->where('karyawan_id', $karyawan->id)
                    ->filter(function ($item) use ($tanggalStr) {

                        return $tanggalStr >= $item->tanggal_mulai
                            && $tanggalStr <= $item->tanggal_selesai;
                    })
                    ->isNotEmpty();

                if ($adaCuti) {
                    $cuti++;
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | CEK ABSENSI
                |--------------------------------------------------------------------------
                */
                $absen = $absensiList
                    ->where('karyawan_id', $karyawan->id)
                    ->where('tanggal', $tanggalStr)
                    ->first();

                if ($absen && !is_null($absen->jam_masuk)) {

                    $hadir++;

                    if ($absen->status_masuk === 'terlambat') {
                        $terlambat++;
                    }

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | JIKA TIDAK HADIR = ALPHA
                |--------------------------------------------------------------------------
                */
                $alpha++;
            }

            $data[] = [
                $karyawan->user->nama ?? '-',
                $hariKerja,
                $hadir,
                $terlambat,
                $izin,
                $cuti,
                $alpha
            ];
        }

        return new Collection($data);
    }

    public function headings(): array
    {
        return [
            'Nama Karyawan',
            'Total Hari Kerja',
            'Hadir',
            'Terlambat (dari hadir)',
            'Izin',
            'Cuti',
            'Alpha'
        ];
    }

    public function title(): string
    {
        return 'Rekap Absensi';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 14
                ]
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet;

                $sheet->insertNewRowBefore(1, 3);

                $sheet->mergeCells('A1:G1');
                $sheet->setCellValue('A1', 'LAPORAN REKAP ABSENSI KARYAWAN');

                $sheet->mergeCells('A2:G2');
                $sheet->setCellValue(
                    'A2',
                    'Periode : ' . $this->tanggalMulai . ' s/d ' . $this->tanggalSelesai
                );
            }
        ];
    }
}
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

        // Ambil data izin dan cuti approved untuk periode ini
        $izinList = Izin::where('status', 'approved')
            ->where(function ($q) {
                $q->whereBetween('tanggal_mulai', [$this->tanggalMulai, $this->tanggalSelesai])
                    ->orWhereBetween('tanggal_selesai', [$this->tanggalMulai, $this->tanggalSelesai])
                    ->orWhere(function ($q2) {
                        $q2->where('tanggal_mulai', '<=', $this->tanggalMulai)
                            ->where('tanggal_selesai', '>=', $this->tanggalSelesai);
                    });
            })
            ->get();

        $cutiList = Cuti::where('status', 'approved')
            ->where(function ($q) {
                $q->whereBetween('tanggal_mulai', [$this->tanggalMulai, $this->tanggalSelesai])
                    ->orWhereBetween('tanggal_selesai', [$this->tanggalMulai, $this->tanggalSelesai])
                    ->orWhere(function ($q2) {
                        $q2->where('tanggal_mulai', '<=', $this->tanggalMulai)
                            ->where('tanggal_selesai', '>=', $this->tanggalSelesai);
                    });
            })
            ->get();

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

                // Cek apakah ada shift pada tanggal ini
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

                // Jika tidak punya shift, skip tanggal ini
                if (!$punyaShift) {
                    continue;
                }

                $hariKerja++;

                // Format tanggal untuk perbandingan
                $tanggalStr = $tanggal->format('Y-m-d');

                // Cek izin terlebih dahulu
                $adaIzin = $izinList->where('karyawan_id', $karyawan->id)
                    ->filter(function ($item) use ($tanggalStr) {
                        $mulai = Carbon::parse($item->tanggal_mulai)->format('Y-m-d');
                        $selesai = Carbon::parse($item->tanggal_selesai)->format('Y-m-d');
                        return $mulai <= $tanggalStr && $tanggalStr <= $selesai;
                    })
                    ->count() > 0;

                if ($adaIzin) {
                    $izin++;
                    continue;
                }

                // Cek cuti
                $adaCuti = $cutiList->where('karyawan_id', $karyawan->id)
                    ->filter(function ($item) use ($tanggalStr) {
                        $mulai = Carbon::parse($item->tanggal_mulai)->format('Y-m-d');
                        $selesai = Carbon::parse($item->tanggal_selesai)->format('Y-m-d');
                        return $mulai <= $tanggalStr && $tanggalStr <= $selesai;
                    })
                    ->count() > 0;

                if ($adaCuti) {
                    $cuti++;
                    continue;
                }

                // Cek absensi
                $absen = Absensi::where('karyawan_id', $karyawan->id)
                    ->whereDate('tanggal', $tanggalStr)
                    ->first();

                if ($absen && !is_null($absen->jam_masuk)) {
                    $hadir++;

                    if ($absen->status_masuk == 'terlambat') {
                        $terlambat++;
                    }

                    continue;
                }

                // Jika tidak ada izin, cuti, atau absensi dengan jam masuk = alpha
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
            'Terlambat',
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
            1 => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet;

                // Insert 3 rows di awal untuk title dan periode
                $sheet->insertNewRowBefore(1, 3);

                // Set title dan periode
                $sheet->mergeCells('A1:G1');
                $sheet->setCellValue('A1', 'LAPORAN REKAP ABSENSI KARYAWAN');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getRowDimension(1)->setRowHeight(25);

                $sheet->mergeCells('A2:G2');
                $sheet->setCellValue(
                    'A2',
                    'Periode : ' . $this->tanggalMulai . ' s/d ' . $this->tanggalSelesai
                );
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);

                // Row 3 dibiarkan kosong untuk spacing
                $sheet->getRowDimension(3)->setRowHeight(10);
                $sheet->getStyle('A3:G3')->getFill()->setFillType(null);

                // Format heading (row 4 setelah insert)
                $sheet->getStyle('A4:G4')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
                $sheet->getStyle('A4:G4')->getFill()->setFillType('solid')->getStartColor()->setRGB('366092');
                $sheet->getRowDimension(4)->setRowHeight(20);

                // Format borders hanya untuk heading
                $sheet->getStyle('A4:G4')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' =>
                            \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ]
                    ]
                ]);

                // Format borders untuk data rows
                $sheet->getStyle('A5:G1000')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' =>
                            \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ]
                    ]
                ]);

                // Auto size columns
                $sheet->getColumnDimension('A')->setWidth(25);
                $sheet->getColumnDimension('B')->setWidth(15);
                $sheet->getColumnDimension('C')->setWidth(12);
                $sheet->getColumnDimension('D')->setWidth(12);
                $sheet->getColumnDimension('E')->setWidth(12);
                $sheet->getColumnDimension('F')->setWidth(12);
                $sheet->getColumnDimension('G')->setWidth(12);
            }
        ];
    }
}

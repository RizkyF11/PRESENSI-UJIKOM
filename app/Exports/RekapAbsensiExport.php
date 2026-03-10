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

class RekapAbsensiExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    ShouldAutoSize,
    WithEvents,
    WithTitle
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
        // Ambil semua absensi sesuai filter
        $query = Absensi::with(['karyawan.user', 'shift'])
            ->whereBetween('tanggal', [$this->tanggalMulai, $this->tanggalSelesai])
            ->orderBy('tanggal', 'asc')
            ->orderBy('karyawan_id', 'asc');

        if ($this->karyawanId) {
            $query->where('karyawan_id', $this->karyawanId);
        }

        $absensiList = $query->get();

        // Ambil data izin dan cuti yang approved
        $izin = Izin::where('status', 'approved')
            ->where(function ($q) {
                $q->whereBetween('tanggal_mulai', [$this->tanggalMulai, $this->tanggalSelesai])
                    ->orWhereBetween('tanggal_selesai', [$this->tanggalMulai, $this->tanggalSelesai])
                    ->orWhere(function ($q2) {
                        $q2->where('tanggal_mulai', '<=', $this->tanggalMulai)
                            ->where('tanggal_selesai', '>=', $this->tanggalSelesai);
                    });
            })
            ->get();

        $cuti = Cuti::where('status', 'approved')
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

        foreach ($absensiList as $item) {
            // Tentukan status
            $status = 'Alpha';

            // Check izin
            if ($izin->where('karyawan_id', $item->karyawan_id)
                ->where('tanggal_mulai', '<=', $item->tanggal)
                ->where('tanggal_selesai', '>=', $item->tanggal)
                ->count()
            ) {
                $status = 'Izin';
            }
            // Check cuti
            elseif ($cuti->where('karyawan_id', $item->karyawan_id)
                ->where('tanggal_mulai', '<=', $item->tanggal)
                ->where('tanggal_selesai', '>=', $item->tanggal)
                ->count()
            ) {
                $status = 'Cuti';
            }
            // Check jika ada jam masuk
            elseif (!is_null($item->jam_masuk)) {
                if ($item->status_masuk === 'terlambat') {
                    $status = 'Terlambat';
                } else {
                    $status = 'Hadir';
                }
            }

            $data[] = [
                $item->karyawan->user->nama ?? '-',
                Carbon::parse($item->tanggal)->format('d-m-Y'),
                $item->jam_masuk ?? '-',
                $item->jam_keluar ?? '-',
                $status,
                $item->shift->nama_shift ?? '-',
            ];
        }

        return new Collection($data);
    }

    public function headings(): array
    {
        return [
            'Nama Karyawan',
            'Tanggal',
            'Jam Masuk',
            'Jam Keluar',
            'Status',
            'Shift',
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
            4 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '366092']]]
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
                $sheet->mergeCells('A1:F1');
                $sheet->setCellValue('A1', 'LAPORAN RIWAYAT ABSENSI KARYAWAN');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getRowDimension(1)->setRowHeight(25);

                $sheet->mergeCells('A2:F2');
                $sheet->setCellValue(
                    'A2',
                    'Periode : ' . $this->tanggalMulai . ' s/d ' . $this->tanggalSelesai
                );
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);

                // Row 3 dibiarkan kosong untuk spacing
                $sheet->getRowDimension(3)->setRowHeight(10);
                $sheet->getStyle('A3:F3')->getFill()->setFillType(null);

                // Format heading (row 4 setelah insert)
                $sheet->getStyle('A4:F4')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
                $sheet->getStyle('A4:F4')->getFill()->setFillType('solid')->getStartColor()->setRGB('366092');
                $sheet->getRowDimension(4)->setRowHeight(20);

                // Format borders hanya untuk heading dan data
                $sheet->getStyle('A4:F4')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' =>
                            \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ]
                    ]
                ]);

                // Format borders untuk data rows
                $sheet->getStyle('A5:F1000')->applyFromArray([
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
                $sheet->getColumnDimension('C')->setWidth(15);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(15);
                $sheet->getColumnDimension('F')->setWidth(20);
            }
        ];
    }
}

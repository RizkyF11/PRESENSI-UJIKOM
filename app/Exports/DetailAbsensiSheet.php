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

class DetailAbsensiSheet implements
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
        $data = [];

        $karyawanList = Karyawan::with('user')
            ->when($this->karyawanId, function ($q) {
                $q->where('id', $this->karyawanId);
            })
            ->get();

        $period = CarbonPeriod::create(
            $this->tanggalMulai,
            $this->tanggalSelesai
        );

        foreach ($karyawanList as $karyawan) {

            foreach ($period as $tanggal) {

                // SKIP WEEKEND
                if ($tanggal->isWeekend()) {
                    continue;
                }

                $tanggalStr = $tanggal->format('Y-m-d');

                $absensi = Absensi::with('shift')
                    ->where('karyawan_id', $karyawan->id)
                    ->whereDate('tanggal', $tanggalStr)
                    ->first();

                $izin = Izin::where('karyawan_id', $karyawan->id)
                    ->where('status', 'approved')
                    ->whereDate('tanggal_mulai', '<=', $tanggalStr)
                    ->whereDate('tanggal_selesai', '>=', $tanggalStr)
                    ->exists();

                $cuti = Cuti::where('karyawan_id', $karyawan->id)
                    ->where('status', 'approved')
                    ->whereDate('tanggal_mulai', '<=', $tanggalStr)
                    ->whereDate('tanggal_selesai', '>=', $tanggalStr)
                    ->exists();

                $status = 'Alpha';
                $jamMasuk = '-';
                $jamKeluar = '-';
                $shiftNama = '-';

                if ($izin) {
                    $status = 'Izin';
                } elseif ($cuti) {
                    $status = 'Cuti';
                } elseif ($absensi) {

                    $jamMasuk = $absensi->jam_masuk ?? '-';
                    $jamKeluar = $absensi->jam_keluar ?? '-';
                    $shiftNama = $absensi->shift->nama_shift ?? '-';

                    if ($absensi->status_masuk == 'terlambat') {
                        $status = 'Terlambat';
                    } else {
                        $status = 'Hadir';
                    }
                }

                $data[] = [
                    $karyawan->user->nama ?? '-',
                    $tanggal->format('d-m-Y'),
                    $jamMasuk,
                    $jamKeluar,
                    $status,
                    $shiftNama,
                ];
            }
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
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet;

                $sheet->insertNewRowBefore(1, 3);

                $sheet->mergeCells('A1:F1');
                $sheet->setCellValue('A1', 'LAPORAN RIWAYAT ABSENSI KARYAWAN');

                $sheet->mergeCells('A2:F2');
                $sheet->setCellValue(
                    'A2',
                    'Periode : ' . $this->tanggalMulai . ' s/d ' . $this->tanggalSelesai
                );

                $sheet->getStyle('A4:F4')->getFont()->setBold(true);
            }
        ];
    }
}
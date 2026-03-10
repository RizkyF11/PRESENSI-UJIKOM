<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RekapAbsensiExport implements WithMultipleSheets
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

    public function sheets(): array
    {
        return [
            new DetailAbsensiSheet(
                $this->tanggalMulai,
                $this->tanggalSelesai,
                $this->karyawanId
            ),

            new RekapAbsensiSheet(
                $this->tanggalMulai,
                $this->tanggalSelesai,
                $this->karyawanId
            )
        ];
    }
}

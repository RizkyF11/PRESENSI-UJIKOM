<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RiwayatAbsensiController extends Controller
{
    public function index(Request $request)
    {
        $karyawanId = Auth::user()->karyawan->id;

        // Ambil semua data absensi karyawan
        $query = Absensi::with('shift')
            ->where('karyawan_id', $karyawanId)
            ->orderBy('tanggal', 'desc');

        // Filter berdasarkan bulan dan tahun jika ada, default ke bulan ini
        $bulan = $request->bulan ?? Carbon::now()->month;
        $tahun = $request->tahun ?? Carbon::now()->year;

        if ($bulan != 'semua') {
            $query->whereMonth('tanggal', $bulan);
        }
        if ($tahun != 'semua') {
            $query->whereYear('tanggal', $tahun);
        }

        $riwayatAbsensi = $query->get();

        // Ambil data Izin dan Cuti untuk perhitungan status riwayat absensi seperti pada admin
        $izin = \App\Models\Izin::where('karyawan_id', $karyawanId)
            ->where('status', 'approved')
            ->get();

        $cuti = \App\Models\Cuti::where('karyawan_id', $karyawanId)
            ->where('status', 'approved')
            ->get();

        return view('karyawan_fe.riwayat_absensi.index', compact('riwayatAbsensi', 'bulan', 'tahun', 'izin', 'cuti'));
    }
}

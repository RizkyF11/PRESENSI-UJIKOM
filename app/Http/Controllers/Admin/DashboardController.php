<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Shift;
use App\Models\Izin;
use App\Models\Cuti;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // 1. Total Karyawan
        $totalKaryawan = Karyawan::where('status', 'aktif')->count();

        // 2. Total Shift
        $totalShift = Shift::where('is_active', true)->count();

        // 3. Pending Izin
        $pendingIzin = Izin::where('status', 'pending')->count();

        // 4. Pending Cuti
        $pendingCuti = Cuti::where('status', 'pending')->count();

        // 5. Absensi Hari Ini (hadir)
        $absensiHariIni = Absensi::whereDate('tanggal', $today)
            ->where('status_masuk', 'hadir')
            ->count();

        // 6. Terlambat Hari Ini
        $terlambatHariIni = Absensi::whereDate('tanggal', $today)
            ->where('status_masuk', 'terlambat')
            ->count();

        // 7. Total seharusnya masuk (karyawan aktif)
        $totalSeharusnyaMasuk = $totalKaryawan;

        // 8. Alpha Hari Ini
        // Karyawan aktif yang tidak punya record absensi hari ini
        // dan tidak sedang izin/cuti approved hari ini
        $karyawanHadirIds = Absensi::whereDate('tanggal', $today)
            ->whereIn('status_masuk', ['hadir', 'terlambat'])
            ->pluck('karyawan_id');

        $karyawanIzinIds = Izin::whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->where('status', 'approved')
            ->pluck('karyawan_id');

        $karyawanCutiIds = Cuti::whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->where('status', 'approved')
            ->pluck('karyawan_id');

        $alphaHariIni = Karyawan::where('status', 'aktif')
            ->whereNotIn('id', $karyawanHadirIds)
            ->whereNotIn('id', $karyawanIzinIds)
            ->whereNotIn('id', $karyawanCutiIds)
            ->count();

        // 9. Karyawan Izin/Cuti Hari Ini (approved)
        $izinHariIni = Izin::with('karyawan.user')
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->where('status', 'approved')
            ->get()
            ->map(function ($item) {
                $item->type = 'Izin';
                return $item;
            });

        $cutiHariIni = Cuti::with('karyawan.user')
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->where('status', 'approved')
            ->get()
            ->map(function ($item) {
                $item->type = 'Cuti';
                return $item;
            });

        $izinCutiHariIni = $izinHariIni->concat($cutiHariIni)->sortBy('karyawan.user.nama');

        // 10. Pending Izin & Cuti dengan detail karyawan
        $pendingIzinData = Izin::with('karyawan.user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($item) {
                $item->type = 'Izin';
                return $item;
            });

        $pendingCutiData = Cuti::with('karyawan.user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($item) {
                $item->type = 'Cuti';
                return $item;
            });

        $pendingPengajuan = $pendingIzinData->concat($pendingCutiData)
            ->sortByDesc('created_at')
            ->take(5);

        // 11. Statistik Kehadiran (Last 7 days)
        $attendanceStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);

            $hadir = Absensi::whereDate('tanggal', $date)
                ->where('status_masuk', 'hadir')
                ->count();

            $terlambat = Absensi::whereDate('tanggal', $date)
                ->where('status_masuk', 'terlambat')
                ->count();

            $sakit = Izin::whereDate('tanggal_mulai', '<=', $date)
                ->whereDate('tanggal_selesai', '>=', $date)
                ->where('status', 'approved')
                ->count();

            $attendanceStats[] = [
                'date'      => $date->format('D'),
                'hadir'     => $hadir,
                'terlambat' => $terlambat,
                'sakit'     => $sakit,
            ];
        }

        // 12. Persentase Kehadiran hari ini
        $hadirDanTerlambat   = $absensiHariIni + $terlambatHariIni;
        $persentaseKehadiran = $totalSeharusnyaMasuk > 0
            ? round(($hadirDanTerlambat / $totalSeharusnyaMasuk) * 100, 1)
            : 0;

        return view('admin.dashboard', compact(
            'totalKaryawan',
            'totalShift',
            'pendingIzin',
            'pendingCuti',
            'absensiHariIni',
            'terlambatHariIni',
            'alphaHariIni',
            'izinCutiHariIni',
            'pendingPengajuan',
            'attendanceStats',
            'persentaseKehadiran',
            'totalSeharusnyaMasuk'
        ));
    }
}
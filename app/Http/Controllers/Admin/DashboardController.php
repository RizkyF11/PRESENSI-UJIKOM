<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Shift;
use App\Models\Izin;
use App\Models\Cuti;
use App\Models\Absensi;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        /*
        |--------------------------------------------------------------------------
        | BASIC STATS
        |--------------------------------------------------------------------------
        */
        $totalKaryawan = Karyawan::where('status', 'aktif')->count();

        $totalShift = Shift::where('is_active', true)->count();

        $pendingIzin = Izin::where('status', 'pending')->count();

        $pendingCuti = Cuti::where('status', 'pending')->count();


        /*
        |--------------------------------------------------------------------------
        | ABSENSI HARI INI
        |--------------------------------------------------------------------------
        */
        $absensiHariIni = Absensi::whereDate('tanggal', $today)
            ->where('status_masuk', 'hadir')
            ->count();

        $terlambatHariIni = Absensi::whereDate('tanggal', $today)
            ->where('status_masuk', 'terlambat')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | WEEKEND LOGIC
        |--------------------------------------------------------------------------
        */
        if ($today->isWeekend()) {

            $totalSeharusnyaMasuk = 0;
            $alphaHariIni = 0;

        } else {

            $totalSeharusnyaMasuk = $totalKaryawan;

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
        }


        /*
        |--------------------------------------------------------------------------
        | IZIN CUTI HARI INI
        |--------------------------------------------------------------------------
        */
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

        $izinCutiHariIni = $izinHariIni
            ->concat($cutiHariIni)
            ->sortBy('karyawan.user.nama');


        /*
        |--------------------------------------------------------------------------
        | PENDING PENGAJUAN
        |--------------------------------------------------------------------------
        */
        $pendingIzinData = Izin::with('karyawan.user')
            ->where('status', 'pending')
            ->latest()
            ->limit(3)
            ->get()
            ->map(function ($item) {
                $item->type = 'Izin';
                return $item;
            });

        $pendingCutiData = Cuti::with('karyawan.user')
            ->where('status', 'pending')
            ->latest()
            ->limit(3)
            ->get()
            ->map(function ($item) {
                $item->type = 'Cuti';
                return $item;
            });

        $pendingPengajuan = $pendingIzinData
            ->concat($pendingCutiData)
            ->sortByDesc('created_at')
            ->take(5);


        /*
        |--------------------------------------------------------------------------
        | ATTENDANCE STATS 7 HARI (SKIP WEEKEND)
        |--------------------------------------------------------------------------
        */
        $attendanceStats = [];

        for ($i = 6; $i >= 0; $i--) {

            $date = Carbon::today()->subDays($i);

            if ($date->isWeekend()) {
                continue;
            }

            $attendanceStats[] = [
                'date' => $date->format('D'),

                'hadir' => Absensi::whereDate('tanggal', $date)
                    ->where('status_masuk', 'hadir')
                    ->count(),

                'terlambat' => Absensi::whereDate('tanggal', $date)
                    ->where('status_masuk', 'terlambat')
                    ->count(),

                'sakit' => Izin::whereDate('tanggal_mulai', '<=', $date)
                    ->whereDate('tanggal_selesai', '>=', $date)
                    ->where('status', 'approved')
                    ->count(),
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | PERSENTASE
        |--------------------------------------------------------------------------
        */
        $hadirDanTerlambat = $absensiHariIni + $terlambatHariIni;

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
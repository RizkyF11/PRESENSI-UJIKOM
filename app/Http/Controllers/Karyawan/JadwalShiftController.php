<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class JadwalShiftController extends Controller
{
    public function index(Request $request)
    {
        $karyawanId = Auth::user()->karyawan->id;

        $bulan = $request->bulan ?? Carbon::now()->month;
        $tahun = $request->tahun ?? Carbon::now()->year;

        // Tanggal awal dan akhir bulan
        $startOfMonth = Carbon::createFromDate($tahun, (int)$bulan, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Ambil semua daftar shift milik karyawan yang aktif di bulan ini
        $karyawanShifts = DB::table('karyawan_shift')
            ->join('shift', 'shift.id', '=', 'karyawan_shift.shift_id')
            ->where('karyawan_shift.karyawan_id', $karyawanId)
            ->whereDate('karyawan_shift.tanggal_mulai', '<=', $endOfMonth->toDateString())
            ->where(function ($q) use ($startOfMonth) {
                $q->whereNull('karyawan_shift.tanggal_selesai')
                    ->orWhereDate('karyawan_shift.tanggal_selesai', '>=', $startOfMonth->toDateString());
            })
            ->select('karyawan_shift.tanggal_mulai', 'karyawan_shift.tanggal_selesai', 'shift.*')
            ->orderBy('karyawan_shift.tanggal_mulai', 'desc') // Yang terbaru nimpa yang lama kalau bentrok
            ->get();

        $calendar = [];
        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);

        foreach ($period as $date) {
            $dateString = $date->toDateString();

            // Cari shift yang sedang berjalan di tanggal ini
            $activeShift = null;
            foreach ($karyawanShifts as $ks) {
                $mulai = Carbon::parse($ks->tanggal_mulai)->startOfDay();
                $selesai = $ks->tanggal_selesai ? Carbon::parse($ks->tanggal_selesai)->endOfDay() : null;

                $isAfterStart = $date->greaterThanOrEqualTo($mulai);
                $isBeforeEnd = $selesai ? $date->lessThanOrEqualTo($selesai) : true;

                if ($isAfterStart && $isBeforeEnd) {
                    $activeShift = $ks;
                    break;
                }
            }

            $calendar[] = [
                'date' => clone $date,
                'shift' => $activeShift
            ];
        }

        $startDayOfWeek = $startOfMonth->dayOfWeek; // 0 = Sunday, 1 = Monday, dll

        return view('karyawan_fe.jadwal_shift.index', compact('calendar', 'bulan', 'tahun', 'startDayOfWeek'));
    }
}

<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\LokasiKantor;
use App\Models\QrCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    public function index()
    {
        $karyawanId = Auth::user()->karyawan->id;
        $now = Carbon::now();
        $bulanIni = $now->month;
        $tahunIni = $now->year;

        // 1. Ambil Shift Hari Ini
        $shiftHariIni = $this->getShiftAktif($karyawanId);

        // 2. Cek Absensi Hari Ini
        $tanggalAbsensi = $shiftHariIni
            ? $this->tentukanTanggalAbsensi($shiftHariIni, $now)
            : $now->toDateString();

        $absensiHariIni = Absensi::where('karyawan_id', $karyawanId)
            ->where('tanggal', $tanggalAbsensi)
            ->first();

        // 3. Statistik Bulan Ini
        $stats = [
            'hadir' => Absensi::where('karyawan_id', $karyawanId)
                ->whereMonth('tanggal', $bulanIni)
                ->whereYear('tanggal', $tahunIni)
                ->where('status_masuk', 'hadir')
                ->count(),

            'terlambat' => Absensi::where('karyawan_id', $karyawanId)
                ->whereMonth('tanggal', $bulanIni)
                ->whereYear('tanggal', $tahunIni)
                ->where('status_masuk', 'terlambat')
                ->count(),

            // Asumsi Izin/Sakit ada tabel sendiri atau flag di absensi, 
            // sementara kita query dari tabel Izin / Cuti (jika ada) atau placeholder 0
            'izin_sakit' => 0, // Implementasi nanti: Izin::where(...)->count()

            'alpha' => 0 // Implementasi nanti logic alpha
        ];

        // 4. Riwayat Absensi (5 Terakhir)
        $riwayatAbsensi = Absensi::with('shift')
            ->where('karyawan_id', $karyawanId)
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get();

        return view('karyawan_fe.dashboard', [
            'shiftHariIni' => $shiftHariIni,
            'absensiHariIni' => $absensiHariIni,
            'stats' => $stats,
            'riwayatAbsensi' => $riwayatAbsensi,
            'tanggal' => Carbon::parse($tanggalAbsensi)->translatedFormat('l, d F Y')
        ]);
    }
    /* =======================
     |  STORE SCAN (Unified)
     =======================*/
    public function storeScan(Request $request)
    {
        $request->validate([
            'tipe_scan' => 'required|in:masuk,keluar',
        ]);

        if ($request->tipe_scan === 'masuk') {
            return $this->scanMasuk($request);
        } else {
            return $this->scanKeluar($request);
        }
    }

    /* =======================
     |  SCAN MASUK
     =======================*/
    public function scanMasuk(Request $request)
    {
        // ... (existing scanMasuk logic, logic re-used)
        // copy ulang validasi awal jika perlu, atau skip karena sudah dipanggil storeScan

        $request->validate([
            'kode'      => 'required|string',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $karyawanId = Auth::user()->karyawan->id;
        $now        = Carbon::now();

        DB::beginTransaction();
        try {

            $shift = $this->getShiftAktif($karyawanId);
            if (! $shift) {
                return $this->error('Anda tidak memiliki shift aktif hari ini');
            }

            $tanggalAbsensi = $this->tentukanTanggalAbsensi($shift, $now);

            // Cek apakah sudah absen masuk
            if ($this->sudahAbsenMasuk($karyawanId, $tanggalAbsensi)) {
                return $this->error('Anda sudah absen masuk hari ini');
            }

            $qr     = $this->validasiQr($request->kode, 'masuk');
            $lokasi = $this->validasiLokasi($request);

            // status hadir / terlambat
            $batasTerlambat = Carbon::parse($tanggalAbsensi . ' ' . $shift->jam_masuk)
                ->addMinutes($shift->toleransi_menit);

            $statusMasuk = $now->greaterThan($batasTerlambat) ? 'terlambat' : 'hadir';

            Absensi::create([
                'karyawan_id'     => $karyawanId,
                'shift_id'        => $shift->id,
                'lokasi_kantor_id' => $lokasi->id,
                'qr_code_id'      => $qr->id,
                'tanggal'         => $tanggalAbsensi,
                'jam_masuk'       => $now->format('H:i:s'),
                'latitude_masuk'        => $request->latitude,
                'longitude_masuk'       => $request->longitude,
                'status_masuk'          => $statusMasuk,
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Absen masuk berhasil',
                'data'    => [
                    'shift'      => $shift->nama_shift,
                    'tanggal'    => $tanggalAbsensi,
                    'jam_masuk'  => $now->format('H:i:s'),
                    'status_masuk'     => $statusMasuk,
                ]
            ]);
        } catch (\Throwable $e) {
            DB::rollback();
            return $this->exception($e);
        }
    }

    /* =======================
     |  SCAN KELUAR
     =======================*/
    public function scanKeluar(Request $request)
    {
        $request->validate([
            'kode'      => 'required|string',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $karyawanId = Auth::user()->karyawan->id;
        $now        = Carbon::now();

        DB::beginTransaction();
        try {

            $shift = $this->getShiftAktif($karyawanId);
            if (! $shift) {
                return $this->error('Shift tidak ditemukan');
            }

            $tanggalAbsensi = $this->tentukanTanggalAbsensi($shift, $now);

            $absensi = Absensi::where('karyawan_id', $karyawanId)
                ->where('tanggal', $tanggalAbsensi)
                ->whereNotNull('jam_masuk')
                ->whereNull('jam_keluar')
                ->first();

            if (! $absensi) {
                // Cek kenapa gagal
                // 1. Belum absen masuk
                // 2. Sudah absen keluar
                $cekSudahKeluar = Absensi::where('karyawan_id', $karyawanId)
                    ->where('tanggal', $tanggalAbsensi)
                    ->whereNotNull('jam_keluar')
                    ->exists();

                if ($cekSudahKeluar) {
                    return $this->error('Anda sudah absen keluar hari ini');
                }

                return $this->error('Anda belum melakukan absen masuk');
            }

            $qr     = $this->validasiQr($request->kode, 'keluar');
            $lokasi = $this->validasiLokasi($request);

            // === STATUS PULANG ===
            $jamKeluarShift = Carbon::parse($tanggalAbsensi . ' ' . $shift->jam_keluar);
            if ($this->isShiftLintasHari($shift)) {
                $jamKeluarShift->addDay();
            }

            $statusKeluar = $now->lessThan($jamKeluarShift)
                ? 'pulang_cepat'
                : 'pulang';

            $absensi->update([
                'jam_keluar'       => $now->format('H:i:s'),
                'latitude_keluar'  => $request->latitude,
                'longitude_keluar' => $request->longitude,
                'status_keluar'           => $statusKeluar,
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Absen keluar berhasil',
                'data'    => [
                    'shift'      => $shift->nama_shift,
                    'tanggal'    => $tanggalAbsensi,
                    'jam_keluar' => $now->format('H:i:s'),
                    'status_keluar'     => $statusKeluar,
                ]
            ]);
        } catch (\Throwable $e) {
            DB::rollback();
            return $this->exception($e);
        }
    }

    /* =======================
     |  HELPER
     =======================*/

    private function getShiftAktif($karyawanId)
    {
        return DB::table('karyawan_shift')
            ->join('shift', 'shift.id', '=', 'karyawan_shift.shift_id')
            ->where('karyawan_shift.karyawan_id', $karyawanId)
            ->whereDate('karyawan_shift.tanggal_mulai', '<=', now()->toDateString())
            ->where(function ($q) {
                $q->whereNull('tanggal_selesai')
                    ->orWhereDate('tanggal_selesai', '>=', now()->toDateString());
            })
            ->select('shift.*')
            ->first();
    }

    private function tentukanTanggalAbsensi($shift, Carbon $now)
    {
        if ($this->isShiftLintasHari($shift) && $now->hour < 12) {
            return $now->copy()->subDay()->toDateString();
        }
        return $now->toDateString();
    }

    private function isShiftLintasHari($shift)
    {
        return Carbon::createFromTimeString($shift->jam_keluar)
            ->lessThan(Carbon::createFromTimeString($shift->jam_masuk));
    }

    private function sudahAbsenMasuk($karyawanId, $tanggal)
    {
        return Absensi::where('karyawan_id', $karyawanId)
            ->where('tanggal', $tanggal)
            ->whereNotNull('jam_masuk')
            ->exists();
    }

    private function validasiQr($kode, $tipe)
    {
        $qr = QrCode::where('kode', $kode)
            ->where('tipe', $tipe)
            ->where('is_active', true)
            ->where('expired_at', '>', now())
            ->first();

        if (! $qr) {
            abort(response()->json([
                'status' => 'error',
                'message' => 'QR Code tidak valid atau expired'
            ], 422));
        }

        return $qr;
    }

    private function validasiLokasi(Request $request)
    {
        $lokasi = LokasiKantor::where('is_active', true)->first();

        if (! $lokasi) {
            abort(500, 'Lokasi kantor belum disetting');
        }

        $jarak = $this->hitungJarak(
            $request->latitude,
            $request->longitude,
            $lokasi->latitude,
            $lokasi->longitude
        );

        if ($jarak > $lokasi->radius) {
            abort(response()->json([
                'status' => 'error',
                'message' => 'Anda berada di luar radius kantor'
            ], 422));
        }

        return $lokasi;
    }

    private function hitungJarak($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) ** 2;

        return $earthRadius * (2 * atan2(sqrt($a), sqrt(1 - $a)));
    }

    private function error($message)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], 422);
    }

    private function exception(\Throwable $e)
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Terjadi kesalahan sistem',
            'error'   => $e->getMessage()
        ], 500);
    }
}

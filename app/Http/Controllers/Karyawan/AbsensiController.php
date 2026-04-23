<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\LokasiKantor;
use App\Models\QrCode;
use App\Services\GamificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AbsensiController extends Controller
{
    /**
     * Menampilkan halaman dashboard karyawan.
     *
     * Mengambil data shift hari ini, absensi hari ini, statistik kehadiran
     * bulan berjalan, dan 5 riwayat absensi terakhir milik karyawan yang login.
     */
    public function index()
    {
        $karyawanId = Auth::user()->karyawan->id;

        // object/ instance
        $now = Carbon::now();

        $bulanIni = $now->month;
        $tahunIni = $now->year;

        // 1. Ambil Shift Hari Ini
        $shiftHariIni = $this->getShiftHariIni($karyawanId);

        $tanggalAbsensi = null;
        $absensiHariIni = null;

        if ($shiftHariIni) {

            $tanggalAbsensi = $this->tentukanTanggalAbsensi($shiftHariIni, $now);

            // Filter berdasarkan shift_id juga
            $absensiHariIni = Absensi::where('karyawan_id', $karyawanId)
                ->where('shift_id', $shiftHariIni->id)
                ->where('tanggal', $tanggalAbsensi)
                ->first();
        }

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
            'tanggal' => $tanggalAbsensi
                ? Carbon::parse($tanggalAbsensi)->translatedFormat('l, d F Y')
                : Carbon::now()->translatedFormat('l, d F Y'),
        ]);
    }
    /* =======================
     |  STORE SCAN (Unified)
     =======================*/
    /**
     * Entry point terpadu untuk scan absensi masuk maupun keluar.
     *
     * Meneruskan request ke metode yang sesuai berdasarkan nilai `tipe_scan`.
     */
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
    /**
     * Memproses absensi masuk karyawan melalui scan QR Code.
     *
     * Alur:
     * 1. Validasi shift aktif karyawan saat ini.
     * 2. Cegah double scan masuk pada shift dan tanggal yang sama.
     * 3. Validasi QR Code (tipe masuk, aktif, belum expired).
     * 4. Validasi lokasi (karyawan harus berada dalam radius kantor).
     * 5. Tentukan status masuk: 'hadir' atau 'terlambat'.
     * 6. Simpan record absensi dalam transaksi database.
     * 7. Jalankan evaluasi gamifikasi setelah transaksi berhasil.
     */
    public function scanMasuk(Request $request)
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
                return $this->error('Anda tidak memiliki shift aktif saat ini');
            }

            $tanggalAbsensi = $this->tentukanTanggalAbsensi($shift, $now);

            //  Cegah double scan masuk
            $sudahMasuk = Absensi::where('karyawan_id', $karyawanId)
                ->where('shift_id', $shift->id)
                ->where('tanggal', $tanggalAbsensi)
                ->whereNotNull('jam_masuk')
                ->exists();

            if ($sudahMasuk) {
                return $this->error('Anda sudah absen masuk untuk shift ini');
            }

            $qr     = $this->validasiQr($request->kode, 'masuk');
            $lokasi = $this->validasiLokasi($request);

            // ===== VALIDASI TERLAMBAT =====
            $batasTerlambat = Carbon::parse($tanggalAbsensi . ' ' . $shift->jam_masuk)
                ->addMinutes($shift->toleransi_menit ?? 0);

            // Fix ✅
            $statusMasuk = $now->greaterThanOrEqualTo($batasTerlambat)
                ? 'terlambat'
                : 'hadir';

            $absensi = Absensi::create([
                'karyawan_id'      => $karyawanId,
                'shift_id'         => $shift->id,
                'lokasi_kantor_id' => $lokasi->id,
                'qr_code_id'       => $qr->id,
                'tanggal'          => $tanggalAbsensi,
                'jam_masuk'        => $now->format('H:i:s'),
                'latitude_masuk'   => $request->latitude,
                'longitude_masuk'  => $request->longitude,
                'status_masuk'     => $statusMasuk,
            ]);
            $absensi->load('shift', 'karyawan.user');


            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            return $this->exception($e);
        }
        // Gamification SETELAH transaction absensi selesai
        if ($absensi) {
            try {
                $absensi->load('shift', 'karyawan.user');
                app(GamificationService::class)->evaluateAndRecord($absensi);
            } catch (\Throwable $e) {
                Log::error('Gamification gagal absensi #' . $absensi->id . ': ' . $e->getMessage());
                // Absensi tetap sukses, gamification gagal tidak rollback absensi
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Absen masuk berhasil',
        ]);
    }


    /* =======================
     |  SCAN KELUAR
     =======================*/
    /**
     * Memproses absensi keluar karyawan melalui scan QR Code.
     *
     * Alur:
     * 1. Validasi shift aktif karyawan saat ini.
     * 2. Pastikan karyawan sudah melakukan absen masuk terlebih dahulu.
     * 3. Cegah double scan keluar.
     * 4. Validasi QR Code (tipe keluar, aktif, belum expired).
     * 5. Validasi lokasi karyawan.
     * 6. Tentukan status keluar: 'pulang_cepat' atau 'pulang'.
     *    - 'pulang_cepat' : scan dilakukan dalam 15 menit sebelum jam pulang.
     *    - 'pulang'       : scan dilakukan pada atau setelah jam pulang.
     * 7. Update record absensi.
     */
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
                ->where('shift_id', $shift->id)
                ->where('tanggal', $tanggalAbsensi)
                ->first();

            if (! $absensi) {
                return $this->error('Anda belum melakukan absen masuk');
            }

            if ($absensi->jam_keluar) {
                return $this->error('Anda sudah absen keluar');
            }

            $qr     = $this->validasiQr($request->kode, 'keluar');
            $lokasi = $this->validasiLokasi($request);


            // ===== STATUS PULANG =====
            $jamKeluarShift = Carbon::parse($tanggalAbsensi . ' ' . $shift->jam_keluar);

            if ($this->isShiftLintasHari($shift)) {
                $jamKeluarShift->addDay();
            }

            // Batas boleh pulang cepat (30 menit sebelum jam pulang)
            $batasPulangCepat = $jamKeluarShift->copy()->subMinutes(15);

            //  Kalau terlalu cepat (belum masuk batas pulang cepat)
            if ($now->lessThan($batasPulangCepat)) {
                return $this->error('Belum bisa absen keluar. Minimal 15 menit sebelum jam pulang.');
            }

            // Jika sebelum jam pulang tapi sudah masuk 30 menit terakhir
            if ($now->between($batasPulangCepat, $jamKeluarShift)) {
                $statusKeluar = 'pulang_cepat';
            } else {
                $statusKeluar = 'pulang';
            }

            $absensi->update([
                'jam_keluar'       => $now->format('H:i:s'),
                'latitude_keluar'  => $request->latitude,
                'longitude_keluar' => $request->longitude,
                'status_keluar'    => $statusKeluar,
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Absen keluar berhasil',
            ]);
        } catch (\Throwable $e) {
            DB::rollback();
            return $this->exception($e);
        }
    }


    /* =======================
     |  HELPER
     =======================*/
    /**
     * Mengambil shift karyawan yang sedang aktif berdasarkan waktu sekarang.
     *
     * Shift dianggap aktif jika:
     * - Hari ini bukan akhir pekan (Sabtu/Minggu).
     * - Tanggal hari ini berada dalam rentang tanggal_mulai - tanggal_selesai shift.
     * - Jam sekarang berada dalam jendela absen (sejak 60 menit sebelum jam masuk
     *   hingga jam keluar shift).
     * - Untuk shift lintas hari: jendela absen tetap berlaku meski melewati tengah malam.
     *
     * @param  int  $karyawanId
     * @return object|null  Row shift atau null jika tidak ada shift aktif
     */

    private function getShiftAktif($karyawanId)
    {
        $now = Carbon::now();

        if ($now->isWeekend()) {
            return null;
        }
        $today = $now->toDateString();
        $jamSekarang = $now->format('H:i:s');

        $shifts = DB::table('karyawan_shift')
            ->join('shift', 'shift.id', '=', 'karyawan_shift.shift_id')
            ->where('karyawan_shift.karyawan_id', $karyawanId)
            ->whereDate('karyawan_shift.tanggal_mulai', '<=', $today)
            ->where(function ($q) use ($today) {
                // [!] PERBAIKAN DI SINI: Tambahkan prefix karyawan_shift.
                $q->whereNull('karyawan_shift.tanggal_selesai')
                    ->orWhereDate('karyawan_shift.tanggal_selesai', '>=', $today);
            })
            ->select('shift.*')
            ->get();

        foreach ($shifts as $shift) {

            $jamMasuk  = Carbon::createFromTimeString($shift->jam_masuk);
            $jamKeluar = Carbon::createFromTimeString($shift->jam_keluar);

            // BUKA GERBANG ABSEN 60 MENIT LEBIH AWAL
            $jamMulaiBisaAbsen = $jamMasuk->copy()->subMinutes(60)->format('H:i:s');

            // Shift normal
            if ($jamKeluar->greaterThan($jamMasuk)) {
                if ($jamSekarang >= $jamMulaiBisaAbsen && $jamSekarang <= $shift->jam_keluar) {
                    return $shift;
                }
            }

            // Shift lintas hari
            if ($jamKeluar->lessThan($jamMasuk)) {
                // Logika lintas hari: jika sekarang >= sejam sebelum masuk, ATAU <= jam keluar
                if ($jamSekarang >= $jamMulaiBisaAbsen || $jamSekarang <= $shift->jam_keluar) {
                    return $shift;
                }
            }
        }


        return null;
    }

    /**
     * Mengambil shift karyawan yang berlaku pada hari ini (tanpa memeriksa jam aktif).
     *
     * Digunakan di halaman dashboard untuk menampilkan informasi shift,
     * bukan untuk validasi waktu absen. Akhir pekan mengembalikan null.
     *
     * @param  int  $karyawanId
     * @return object|null  Row shift pertama yang ditemukan, atau null
     */
    private function getShiftHariIni($karyawanId)
    {
        $todayCarbon = Carbon::today();

        // Jika weekend = libur
        if ($todayCarbon->isWeekend()) {
            return null;
        }

        $today = $todayCarbon->toDateString();

        return DB::table('karyawan_shift')
            ->join('shift', 'shift.id', '=', 'karyawan_shift.shift_id')
            ->where('karyawan_shift.karyawan_id', $karyawanId)
            ->whereDate('karyawan_shift.tanggal_mulai', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('karyawan_shift.tanggal_selesai')
                    ->orWhereDate('karyawan_shift.tanggal_selesai', '>=', $today);
            })
            ->select('shift.*')
            ->first();
    }


    /**
     * Memeriksa apakah jam sekarang berada dalam rentang aktif shift.
     *
     * Catatan: metode ini belum menangani shift lintas hari.
     * Untuk validasi absen aktual, gunakan getShiftAktif().
     *
     * @param  object  $shift        Row shift dari database
     * @param  string  $jamSekarang  Waktu dalam format 'H:i:s'
     * @return bool
     */
    private function isShiftAktif($shift, $jamSekarang)
    {
        return $jamSekarang >= $shift->jam_masuk && $jamSekarang <= $shift->jam_keluar;
    }


    /**
     * Menentukan tanggal yang dipakai sebagai kunci absensi.
     *
     * Untuk shift lintas hari (misal 22:00 – 06:00), jika sekarang masih
     * di bawah jam 12 siang maka absensi dianggap milik hari sebelumnya.
     * Untuk shift normal, tanggal absensi adalah tanggal hari ini.
     *
     * @param  object  $shift  Row shift dari database
     * @param  Carbon  $now    Waktu sekarang
     * @return string  Tanggal dalam format 'Y-m-d'
     */
    private function tentukanTanggalAbsensi($shift, Carbon $now)
    {
        if ($this->isShiftLintasHari($shift) && $now->hour < 12) {
            return $now->copy()->subDay()->toDateString();
        }
        return $now->toDateString();
    }

    /**
     * Memeriksa apakah shift melewati tengah malam (lintas hari).
     *
     * Shift dinyatakan lintas hari apabila jam_keluar lebih kecil dari jam_masuk,
     * contoh: masuk 22:00, keluar 06:00.
     *
     * @param  object  $shift  Row shift dari database
     * @return bool
     */
    private function isShiftLintasHari($shift)
    {
        return Carbon::createFromTimeString($shift->jam_keluar)
            ->lessThan(Carbon::createFromTimeString($shift->jam_masuk));
    }

     /**
     * Memeriksa apakah karyawan sudah melakukan absen masuk pada tanggal tertentu.
     *
     * @param  int     $karyawanId
     * @param  string  $tanggal  Format 'Y-m-d'
     * @return bool
     */

    private function sudahAbsenMasuk($karyawanId, $tanggal)
    {
        return Absensi::where('karyawan_id', $karyawanId)
            ->where('tanggal', $tanggal)
            ->whereNotNull('jam_masuk')
            ->exists();
    }


    /**
     * Memvalidasi QR Code yang di-scan oleh karyawan.
     *
     * QR Code dianggap valid jika:
     * - Kode cocok dengan yang tersimpan di database.
     * - Tipe QR sesuai ('masuk' atau 'keluar').
     * - Status aktif (is_active = true).
     * - Belum melewati waktu expired_at.
     *
     * Jika tidak valid, fungsi langsung melakukan abort dengan response JSON 422.
     *
     * @param  string  $kode  Kode QR yang di-scan
     * @param  string  $tipe  'masuk' atau 'keluar'
     * @return QrCode
     */
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


    /**
     * Memvalidasi lokasi karyawan terhadap semua lokasi kantor yang aktif.
     *
     * Menghitung jarak antara koordinat karyawan dengan setiap lokasi kantor
     * menggunakan formula Haversine. Mengembalikan lokasi pertama yang
     * jaraknya masih dalam radius yang diizinkan.
     *
     * Jika tidak ada lokasi kantor aktif: abort 500.
     * Jika karyawan di luar semua radius kantor: abort 422.
     *
     * @param  Request  $request  Harus mengandung latitude & longitude
     * @return LokasiKantor  Lokasi kantor yang cocok
     */
    private function validasiLokasi(Request $request)
    {
        $daftarLokasi = LokasiKantor::where('is_active', true)->get();

        if ($daftarLokasi->isEmpty()) {
            abort(500, 'Lokasi kantor belum disetting');
        }

        foreach ($daftarLokasi as $lokasi) {
            $jarak = $this->hitungJarak(
                $request->latitude,
                $request->longitude,
                $lokasi->latitude,
                $lokasi->longitude
            );

            if ($jarak <= $lokasi->radius) {
                return $lokasi; // Karyawan masuk dalam jangkauan kantor ini
            }
        }

        abort(422, 'Anda berada di luar radius dari semua lokasi kantor');
    }


    /**
     * Menghitung jarak antara dua titik koordinat menggunakan formula Haversine.
     *
     * Formula Haversine memperhitungkan kelengkungan bumi sehingga cocok
     * digunakan untuk menghitung jarak pendek (dalam meter) secara akurat.
     *
     * @param  float  $lat1  Latitude titik pertama (karyawan)
     * @param  float  $lon1  Longitude titik pertama (karyawan)
     * @param  float  $lat2  Latitude titik kedua (kantor)
     * @param  float  $lon2  Longitude titik kedua (kantor)
     * @return float  Jarak dalam meter
     */
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


    /**
     * Mengembalikan JSON response error dengan HTTP status 422.
     *
     * @param  string  $message  Pesan error yang akan ditampilkan ke pengguna
     * @return \Illuminate\Http\JsonResponse
     */
    private function error($message)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], 422);
    }


     /**
     * Menangani exception yang tidak tertangkap dan mengembalikan JSON response 500.
     *
     * Menyertakan pesan error, nama file, dan nomor baris untuk keperluan debugging.
     * Sebaiknya hanya aktif di lingkungan development; pertimbangkan untuk
     * menyembunyikan detail teknis di production.
     *
     * @param  \Throwable  $e  Exception yang ditangkap
     * @return \Illuminate\Http\JsonResponse
     */
    private function exception(\Throwable $e)
    {
        return response()->json([
            'status' => 'error',
            // Tampilkan pesan error asli beserta file & barisnya
            'message' => $e->getMessage() . ' (Line: ' . $e->getLine() . ' di ' . class_basename($e->getFile()) . ')',
            'error'   => $e->getMessage()
        ], 500);
    }
}

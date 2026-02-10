<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QrCode as QrModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdminQrController extends Controller
{
    //menampilkan halaman utama 
    public function index()
    {
        return view('admin.qrcode.generate');
    }

    //logic untuk generate kode unik baru (dpanggil via ajax)

    public function generate(Request $request)
    {
        // Validasi tipe yang dikirim via query parameter
        $tipe = $request->query('tipe');
        if (!in_array($tipe, ['masuk', 'keluar'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tipe QR code tidak valid. Harus masuk atau keluar.'
            ], 400);
        }

        try {
            // 1. nonaktifkan semua qr yg masih aktif sebelumnya dengan tipe yang sama
            QrModel::where('tipe', $tipe)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            // 2. buat string unik baru
            $kodeUnik = Str::random(32) . '-' . time();

            // 3. simpan ke database dengan waktu expired dan tipe
            $qr = QrModel::create([
                'kode' => $kodeUnik,
                'tipe' => $tipe,
                'is_active' => true,
                'expired_at' => now()->addSecond(25), // dilebihkan sedikit untuk buffering
            ]);

            // bungkus payload qr, tambahkan tipe
            $payload = json_encode([
                'type' => 'absensi',
                'qr_type' => $tipe, // info tambahan (opsional)
                'kode' => $qr->kode,
            ]);

            // 4. generate gambar qr code dalam bentuk string SVG
            // Kita bungkus kode unik ini agar nanti saat di-scan isinya jelas
            $qrImage = QrCode::size(300)
                ->color(0, 0, 0)
                ->margin(1)
                ->generate($payload);

            return response()->json([
                'status' => 'success',
                'html' => (string)$qrImage,
                'kode' => $qr->kode,
                'tipe' => $qr->tipe,
                'expired_at' => $qr->expired_at->format('H:i:s'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

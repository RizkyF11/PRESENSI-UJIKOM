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
        return view('admin.qrcode.index');
    }

    //logic untuk generate kode unik baru (dpanggil via ajax)

    public function generate() 
    {
        try {
            // 1. nonaktifkan semua qr masih aktif sebelumnya
            QrModel::where('is_active', true)->update(['is_active' => false]);

            // 2. buat string unik baru
            $kodeUnik = Str::random(32) . '-' . time();

            // 3. simpan ke database dengan waktu expired
            $qr = QrModel::create([
                'kode' => $kodeUnik,
                'is_active' => true,
                'expired_at' => now()->addSecond(25), //dilebihkan sedikit untuk buffering
            ]);

            // 4. generate gambar qr code dalam bentuk string SVG
            // Kita bungkus kode unik ini agar nanti saat di-scan isinya jelas
            $qrImage = QrCode::size(300)
                ->color(0, 0, 0)
                ->margin(1)
                ->generate($qr->kode);

            return response()->json([
                'status' => 'success',
                'html' => $qrImage->toHtml(),
                'kode' => $qr->kode,
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

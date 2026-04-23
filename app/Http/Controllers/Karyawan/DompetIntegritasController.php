<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\FlexibilityItem;
use App\Models\PointLedger;
use App\Models\UserToken;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DompetIntegritasController extends Controller
{
    protected GamificationService $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    public function index()
    {
        $user = Auth::user();

        // Saldo poin saat ini
        $balance = $this->gamificationService->getCurrentBalance($user);

        // Riwayat Mutasi (Ledger)
        $ledgers = PointLedger::where('user_id', $user->id)
            ->with(['absensi', 'userToken.item'])
            ->latest()
            ->get();

        // Marketplace Items (Katalog Penukaran)
        // Kita tampilkan item yang aktif sja misal ada kolom is_active kalo tidak ada ya all
        $items = FlexibilityItem::where('is_active', true)->get();

        // Inventory Token User
        $inventory = UserToken::where('user_id', $user->id)
            ->with('item')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('karyawan_fe.dompet.index', compact('balance', 'ledgers', 'items', 'inventory'));
    }

    // Proses penukaran item di marketplace
    public function redeem(Request $request, $id)
    {
        $user = Auth::user();
        $item = FlexibilityItem::findOrFail($id);

        try {
            $result = $this->gamificationService->redeemToken($user, $item);

            if ($result['success']) {
                return redirect()->route('karyawan.dompet.index')
                    ->with('success', $result['message']);
            }

            return redirect()->route('karyawan.dompet.index')
                ->with('error', $result['message']);
        } catch (\Exception $e) {
            return redirect()->route('karyawan.dompet.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

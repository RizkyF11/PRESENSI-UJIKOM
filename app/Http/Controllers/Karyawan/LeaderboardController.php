<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function index()
    {
        $startMonth = Carbon::now()->startOfMonth();
        $endMonth   = Carbon::now()->endOfMonth();

        // 1. Ambil summary saldo seperti di Admin (Realtime bulan ini)
        $leaderboard = User::select(
                'users.id',
                'users.nama',
                'users.role',
                DB::raw('COALESCE(SUM(
                    CASE 
                        WHEN point_ledgers.transaction_type = "EARN" THEN point_ledgers.amount
                        WHEN point_ledgers.transaction_type IN ("PENALTY","SPEND") THEN -point_ledgers.amount
                        ELSE 0
                    END
                ),0) as current_balance')
            )
            ->with('karyawan')
            ->leftJoin('point_ledgers', function ($join) use ($startMonth, $endMonth) {
                $join->on('users.id', '=', 'point_ledgers.user_id')
                    ->whereBetween('point_ledgers.created_at', [$startMonth, $endMonth]);
            })
            ->where('users.role', 'karyawan')
            ->groupBy('users.id', 'users.nama', 'users.role')
            ->orderByDesc('current_balance')
            ->get();

        return view('karyawan_fe.leaderboard.index', compact('leaderboard'));
    }
}

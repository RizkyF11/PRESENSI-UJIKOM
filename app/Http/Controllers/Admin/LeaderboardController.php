<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointLedger;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function index()
    {
        $startMonth = Carbon::now()->startOfMonth();
        $endMonth   = Carbon::now()->endOfMonth();

        /*
        |--------------------------------------------------------------------------
        | Ambil total poin user bulan ini
        |--------------------------------------------------------------------------
        */
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
                ),0) as total_points')
            )
            ->leftJoin('point_ledgers', function ($join) use ($startMonth, $endMonth) {
                $join->on('users.id', '=', 'point_ledgers.user_id')
                    ->whereBetween('point_ledgers.created_at', [$startMonth, $endMonth]);
            })
            ->whereIn('users.role', ['karyawan', 'manager'])
            ->groupBy('users.id', 'users.nama', 'users.role')
            ->orderByDesc('total_points')
            ->get();

        $topUser = $leaderboard->first();
        $lowUser = $leaderboard->last();

        return view('admin.leaderboard.index', compact(
            'leaderboard',
            'topUser',
            'lowUser'
        ));
    }
}
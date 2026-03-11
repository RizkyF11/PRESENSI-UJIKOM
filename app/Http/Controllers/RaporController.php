<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use Illuminate\Support\Facades\Auth;

class RaporController extends Controller
{
    public function index()
    {
        $assessments = Assessment::where('evaluatee_id', Auth::id())
            ->with(['details.category', 'evaluator.karyawan'])
            ->latest('assessment_date')
            ->get();

        // Rata-rata nilai per kategori untuk grafik radar
        $radarData = $assessments->flatMap->details
            ->groupBy(fn($detail) => $detail->category->nama)
            ->map(fn($details) => round($details->avg('score'), 1));

        return view('assessment.rapor', compact(
            'assessments',
            'radarData'
        ));
    }
}

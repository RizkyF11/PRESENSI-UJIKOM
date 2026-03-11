<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use Illuminate\Support\Facades\Auth;

class RaporController extends Controller
{
    // =============================================
    // INDEX - Karyawan lihat nilai diri sendiri
    // Menampilkan grafik radar + history feedback
    // =============================================
    public function index()
    {
        $assessments = Assessment::where('evaluatee_id', Auth::id())
            ->with([
                'evaluator.karyawan',
                'details.question.category' // ← lewat question dulu baru category
            ])
            ->latest('assessment_date')
            ->get();

        // Rata-rata nilai per KATEGORI untuk grafik radar
        // Alur: ambil semua details → groupBy nama kategori → avg score
        // $detail->question->category->nama = nama kategori
        $radarData = $assessments->flatMap->details
            ->groupBy(fn($detail) => $detail->question->category->nama)
            ->map(fn($details) => round($details->avg('score'), 1));

        return view('assessment.rapor', compact(
            'assessments',
            'radarData'
        ));
    }
}
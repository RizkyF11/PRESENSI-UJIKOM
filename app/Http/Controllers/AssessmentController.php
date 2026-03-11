<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentCategory;
use App\Models\AssessmentDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{
    // =============================================
    // INDEX - Dashboard Manager
    // Card list karyawan + progress bar
    // =============================================
    public function index()
    {
        $karyawans = User::where('role', 'karyawan')
            ->with('karyawan')
            ->get();

        $sudahDinilaiIds = Assessment::where('evaluator_id', Auth::id())
            ->whereMonth('assessment_date', now()->month)
            ->whereYear('assessment_date', now()->year)
            ->pluck('evaluatee_id')
            ->toArray();

        $totalKaryawan = $karyawans->count();
        $totalDinilai  = count($sudahDinilaiIds);
        $persentase    = $totalKaryawan > 0
            ? round(($totalDinilai / $totalKaryawan) * 100)
            : 0;

        return view('assessment.manager.index', compact(
            'karyawans',
            'sudahDinilaiIds',
            'totalKaryawan',
            'totalDinilai',
            'persentase'
        ));
    }

    // =============================================
    // CREATE - Form penilaian karyawan baru
    // =============================================
    public function create(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:users,id'
        ]);

        $karyawan = User::where('role', 'karyawan')
            ->with('karyawan')
            ->findOrFail($request->karyawan_id);

        $categories = AssessmentCategory::where('is_active', true)->get();

        // Cek sudah dinilai bulan ini
        $existing = Assessment::where('evaluator_id', Auth::id())
            ->where('evaluatee_id', $karyawan->id)
            ->whereMonth('assessment_date', now()->month)
            ->whereYear('assessment_date', now()->year)
            ->with('details')
            ->first();

        // Karyawan berikutnya yang belum dinilai
        $sudahDinilaiIds = Assessment::where('evaluator_id', Auth::id())
            ->whereMonth('assessment_date', now()->month)
            ->whereYear('assessment_date', now()->year)
            ->pluck('evaluatee_id')
            ->toArray();

        $berikutnya = User::where('role', 'karyawan')
            ->whereNotIn('id', $sudahDinilaiIds)
            ->where('id', '!=', $karyawan->id)
            ->with('karyawan')
            ->first();

        return view('assessment.manager.form', compact(
            'karyawan',
            'categories',
            'existing',
            'berikutnya'
        ))->with('existingScores', []);
    }

    // =============================================
    // STORE - Simpan penilaian baru
    // Header → assessments
    // Detail scores → assessment_details
    // =============================================
    public function store(Request $request)
    {
        $request->validate([
            'evaluatee_id'  => 'required|exists:users,id',
            'period'        => 'required|string|max:255',
            'general_notes' => 'nullable|string',
            'scores'        => 'required|array|min:1',
            'scores.*'      => 'required|integer|min:1|max:5',
        ]);

        // Cek sudah dinilai bulan ini
        $existing = Assessment::where('evaluator_id', Auth::id())
            ->where('evaluatee_id', $request->evaluatee_id)
            ->whereMonth('assessment_date', now()->month)
            ->whereYear('assessment_date', now()->year)
            ->first();

        if ($existing) {
            return redirect()->route('manager.assessment.index')
                ->with('error', 'Karyawan ini sudah dinilai bulan ini!');
        }

        // STEP 1 - Simpan header ke tabel assessments
        $assessment = Assessment::create([
            'evaluator_id'    => Auth::id(),
            'evaluatee_id'    => $request->evaluatee_id,
            'assessment_date' => now(),
            'period'          => $request->period,
            'general_notes'   => $request->general_notes,
        ]);

        // STEP 2 - Simpan detail ke tabel assessment_details
        foreach ($request->scores as $categoryId => $score) {
            $assessment->details()->create([
                'category_id' => $categoryId,
                'score'       => $score,
            ]);
        }

        // Tombol "Simpan & Lanjut ke Orang Berikutnya"
        if ($request->has('next')) {
            $sudahDinilaiIds = Assessment::where('evaluator_id', Auth::id())
                ->whereMonth('assessment_date', now()->month)
                ->whereYear('assessment_date', now()->year)
                ->pluck('evaluatee_id')
                ->toArray();

            $berikutnya = User::where('role', 'karyawan')
                ->whereNotIn('id', $sudahDinilaiIds)
                ->first();

            if ($berikutnya) {
                return redirect()->route('manager.assessment.create', [
                    'karyawan_id' => $berikutnya->id
                ])->with('success', 'Tersimpan! Lanjut menilai karyawan berikutnya.');
            }

            return redirect()->route('manager.assessment.index')
                ->with('success', 'Semua karyawan sudah dinilai bulan ini! 🎉');
        }

        return redirect()->route('manager.assessment.index')
            ->with('success', 'Penilaian berhasil disimpan!');
    }

    // =============================================
    // EDIT - Form edit penilaian yang sudah ada
    // =============================================
    public function edit(Assessment $assessment)
    {
        // Hanya manager pemilik yang bisa edit
        if ($assessment->evaluator_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengubah penilaian ini!');
        }

        $karyawan   = User::with('karyawan')
                        ->findOrFail($assessment->evaluatee_id);

        $categories = AssessmentCategory::where('is_active', true)->get();

        // Map nilai lama [category_id => score]
        $existingScores = $assessment->details
            ->pluck('score', 'category_id')
            ->toArray();
        
        $existing = null;
        $berikutnya = null;

        return view('assessment.manager.form', compact(
            'karyawan',
            'categories',
            'assessment',
            'existingScores',
            'existing',
            'berikutnya'
        ));
    }

    // =============================================
    // UPDATE - Update penilaian yang sudah ada
    // =============================================
    public function update(Request $request, Assessment $assessment)
    {
        // Hanya manager pemilik yang bisa update
        if ($assessment->evaluator_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengubah penilaian ini!');
        }

        $request->validate([
            'period'        => 'required|string|max:255',
            'general_notes' => 'nullable|string',
            'scores'        => 'required|array|min:1',
            'scores.*'      => 'required|integer|min:1|max:5',
        ]);

        // Update header di tabel assessments
        $assessment->update([
            'period'        => $request->period,
            'general_notes' => $request->general_notes,
        ]);

        // Hapus detail lama lalu insert ulang
        $assessment->details()->delete();

        foreach ($request->scores as $categoryId => $score) {
            $assessment->details()->create([
                'category_id' => $categoryId,
                'score'       => $score,
            ]);
        }

        return redirect()->route('manager.assessment.index')
            ->with('success', 'Penilaian berhasil diupdate!');
    }

    // =============================================
    // DESTROY - Hapus penilaian
    // Manager hanya bisa hapus miliknya sendiri
    // Admin bisa hapus semua
    // =============================================
    public function destroy(Assessment $assessment)
    {
        if (Auth::user()->role === 'manager' && $assessment->evaluator_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak menghapus penilaian ini!');
        }

        $assessment->delete();

        return redirect()->back()
            ->with('success', 'Penilaian berhasil dihapus!');
    }

    // =============================================
    // RAPOR - Karyawan lihat nilai diri sendiri
    // Grafik radar + history feedback
    // =============================================
    public function rapor()
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

    // =============================================
    // LAPORAN ADMIN - Lihat semua penilaian
    // Read only
    // =============================================
    public function laporanAdmin()
    {
        $assessments = Assessment::with([
            'evaluator.karyawan',
            'evaluatee.karyawan',
            'details.category'
        ])
        ->latest('assessment_date')
        ->get();

        return view('assessment.laporan', compact('assessments'));
    }
}

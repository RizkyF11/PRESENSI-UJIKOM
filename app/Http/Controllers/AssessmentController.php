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

        // Ambil kategori aktif beserta pertanyaan aktifnya (eager load)
        $categories = AssessmentCategory::with('activeQuestions')
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get();

        // Karyawan berikutnya yang belum dinilai bulan ini
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

        // existingScores kosong karena ini form baru
        $existingScores = [];
        $assessment     = null;

        return view('assessment.manager.form', compact(
            'karyawan',
            'categories',
            'existingScores',
            'assessment',
            'berikutnya'
        ));
    }

    // =============================================
    // STORE - Simpan penilaian baru
    // scores[] dikirim per question_id
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

        // Simpan header ke tabel assessments
        $assessment = Assessment::create([
            'evaluator_id'    => Auth::id(),
            'evaluatee_id'    => $request->evaluatee_id,
            'assessment_date' => now(),
            'period'          => $request->period,
            'general_notes'   => $request->general_notes,
        ]);

        // Simpan detail per question_id ke assessment_details
        foreach ($request->scores as $questionId => $score) {
            $assessment->details()->create([
                'question_id' => $questionId,
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
        if ($assessment->evaluator_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengubah penilaian ini!');
        }

        $karyawan = User::with('karyawan')
            ->findOrFail($assessment->evaluatee_id);

        // Ambil kategori aktif beserta pertanyaan aktifnya
        $categories = AssessmentCategory::with('activeQuestions')
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get();

        // Map nilai lama [question_id => score]
        // Dipakai di blade untuk menampilkan bintang yang sudah dipilih
        $existingScores = $assessment->details
            ->pluck('score', 'question_id')
            ->toArray();

        $berikutnya = null;

        return view('assessment.manager.form', compact(
            'karyawan',
            'categories',
            'assessment',
            'existingScores',
            'berikutnya'
        ));
    }

    // =============================================
    // UPDATE - Update penilaian yang sudah ada
    // =============================================
    public function update(Request $request, Assessment $assessment)
    {
        if ($assessment->evaluator_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengubah penilaian ini!');
        }

        $request->validate([
            'period'        => 'required|string|max:255',
            'general_notes' => 'nullable|string',
            'scores'        => 'required|array|min:1',
            'scores.*'      => 'required|integer|min:1|max:5',
        ]);

        $assessment->update([
            'period'        => $request->period,
            'general_notes' => $request->general_notes,
        ]);

        // Hapus detail lama lalu insert ulang
        $assessment->details()->delete();

        foreach ($request->scores as $questionId => $score) {
            $assessment->details()->create([
                'question_id' => $questionId,
                'score'       => $score,
            ]);
        }

        return redirect()->route('manager.assessment.index')
            ->with('success', 'Penilaian berhasil diupdate!');
    }

    // =============================================
    // DESTROY - Hapus penilaian
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
    // LAPORAN ADMIN - Lihat semua penilaian (read only)
    // =============================================
    public function laporanAdmin()
    {
        $assessments = Assessment::with([
            'evaluator.karyawan',
            'evaluatee.karyawan',
            'details.question.category'
        ])
            ->latest('assessment_date')
            ->get();

        return view('assessment.laporan', compact('assessments'));
    }
}
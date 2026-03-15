<?php

namespace App\Http\Controllers;

use App\Models\AssessmentCategory;
use App\Models\AssessmentQuestion;
use Illuminate\Http\Request;
class AssessmentQuestionController extends Controller
{
    // =============================================
    // INDEX - Tampilkan semua pertanyaan milik
    // kategori tertentu
    // URL: /admin/assessment-categories/{category}/questions
    // =============================================
    public function index(AssessmentCategory $assessmentCategory)
    {
        $questions = $assessmentCategory->questions()->orderBy('urutan')->get();

        return view('assessment.questions.index', [
            'category'  => $assessmentCategory,
            'questions' => $questions,
        ]);
    }

    // =============================================
    // CREATE - Form tambah pertanyaan baru
    // $assessmentCategory sudah diketahui dari URL
    // =============================================
    public function create(AssessmentCategory $assessmentCategory)
    {
        // Urutan otomatis: ambil urutan tertinggi + 1
        $nextUrutan = $assessmentCategory->questions()->max('urutan') + 1;

        return view('assessment.questions.form', [
            'category'    => $assessmentCategory,
            'nextUrutan'  => $nextUrutan,
        ]);
    }

    // =============================================
    // STORE - Simpan pertanyaan baru
    // category_id diambil dari $assessmentCategory,
    // bukan dari request (lebih aman)
    // =============================================
    public function store(Request $request, AssessmentCategory $assessmentCategory)
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'urutan'   => 'nullable|integer|min:0',
        ]);

        AssessmentQuestion::create([
            'category_id' => $assessmentCategory->id,  // dari URL, bukan input user
            'question'    => $request->question,
            'urutan'      => $request->urutan ?? 0,
            'is_active'   => true,
        ]);

        return redirect()
            ->route('admin.assessment-questions.index', $assessmentCategory->id)
            ->with('success', 'Pertanyaan berhasil ditambahkan!');
    }

    // =============================================
    // EDIT - Form edit pertanyaan
    // Kedua parameter wajib ada: category & question
    // =============================================
    public function edit(AssessmentCategory $assessmentCategory, AssessmentQuestion $question)
    {
        $nextUrutan = $assessmentCategory->questions()->max('urutan');

        return view('assessment.questions.form', [
            'category'   => $assessmentCategory,
            'question'   => $question,
            'nextUrutan' => $nextUrutan,
        ]);
    }

    // =============================================
    // UPDATE - Simpan perubahan pertanyaan
    // =============================================
    public function update(Request $request, AssessmentCategory $assessmentCategory, AssessmentQuestion $question)
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'urutan'   => 'nullable|integer|min:0',
        ]);

        $question->update([
            'question' => $request->question,
            'urutan'   => $request->urutan ?? $question->urutan,
        ]);

        return redirect()
            ->route('admin.assessment-questions.index', $assessmentCategory->id)
            ->with('success', 'Pertanyaan berhasil diupdate!');
    }

    // =============================================
    // TOGGLE ACTIVE - Nonaktifkan / Aktifkan
    // Data penilaian lama tetap AMAN tidak terhapus
    // =============================================
    public function toggleActive(AssessmentCategory $assessmentCategory, AssessmentQuestion $question)
    {
        $question->update([
            'is_active' => !$question->is_active
        ]);

        $status = $question->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->route('admin.assessment-questions.index', $assessmentCategory->id)
            ->with('success', "Pertanyaan berhasil {$status}!");
    }

    // =============================================
    // DESTROY - Hapus pertanyaan permanen
    // HATI-HATI: semua assessment_details terkait
    // akan IKUT TERHAPUS karena cascade
    // Lebih aman pakai toggleActive()
    // =============================================
    public function destroy(AssessmentCategory $assessmentCategory, AssessmentQuestion $question)
    {
        $question->delete();

        return redirect()
            ->route('admin.assessment-questions.index', $assessmentCategory->id)
            ->with('success', 'Pertanyaan berhasil dihapus permanen!');
    }
}
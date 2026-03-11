<?php

namespace App\Http\Controllers;

use App\Models\AssessmentCategory;
// use App\Models\AssessmentsCategory; --- IGNORE ---
use Illuminate\Http\Request;

class AssessmentCategoryController extends Controller
{
    // =============================================
    // INDEX - Tampilkan semua kategori
    // =============================================
    public function index()
    {
        $categories = AssessmentCategory::latest()->get();
        return view('assessment.categories.index', compact('categories'));
    }

    // =============================================
    // CREATE - Tampilkan form tambah
    // =============================================
    public function create()
    {
        return view('assessment.categories.form');
    }

    // =============================================
    // STORE - Tambah kategori baru
    // =============================================
    public function store(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        AssessmentCategory::create([
            'nama'        => $request->nama,
            'description' => $request->description,
            'is_active'   => true,
        ]);

        return redirect()->route('admin.assessment-categories.index') // ← ganti back() ke ini
            ->with('success', 'Kategori berhasil ditambahkan!');
    }

    // =============================================
    // EDIT - Tampilkan form edit
    // =============================================
    public function edit(AssessmentCategory $assessmentCategory)
    {
        return view('assessment.categories.form', [
            'category' => $assessmentCategory
        ]);
    }


    // =============================================
    // UPDATE - Edit nama & deskripsi kategori
    // =============================================
    public function update(Request $request, AssessmentCategory $assessmentCategory)
    {
        $request->validate([
            'nama'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $assessmentCategory->update([
            'nama'        => $request->nama,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.assessment-categories.index')->with('success', 'Kategori berhasil diupdate!');
    }

    // =============================================
    // TOGGLE ACTIVE - Nonaktifkan / Aktifkan kategori
    // Data penilaian lama tetap AMAN
    // =============================================
    public function toggleActive(AssessmentCategory $assessmentCategory)
    {
        $assessmentCategory->update([
            'is_active' => !$assessmentCategory->is_active
        ]);

        $status = $assessmentCategory->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()->with('success', "Kategori berhasil {$status}!");
    }

    // =============================================
    // DESTROY - Hapus permanen
    // Data penilaian lama yang pakai kategori
    // ini akan IKUT TERHAPUS (cascade)
    // =============================================
    public function destroy(AssessmentCategory $assessmentCategory)
    {
        $name = $assessmentCategory->name;

        $assessmentCategory->delete();

        return redirect()->back()->with('success', "Kategori berhasil dihapus permanen!");
    }
}

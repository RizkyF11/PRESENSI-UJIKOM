<?php

namespace App\Http\Controllers;

use App\Models\AssessmentCategory;
use Illuminate\Http\Request;
class AssessmentCategoryController extends Controller
{
    // =============================================
    // INDEX - Tampilkan semua kategori
    // Tampil beserta jumlah pertanyaannya
    // =============================================
    public function index()
    {
        // withCount untuk tampilkan "X pertanyaan" di tabel
        $categories = AssessmentCategory::withCount('questions')
            ->orderBy('urutan')
            ->get();

        return view('assessment.categories.index', compact('categories'));
    }

    // =============================================
    // CREATE - Tampilkan form tambah kategori
    // =============================================
    public function create()
    {
        return view('assessment.categories.form');
    }

    // =============================================
    // STORE - Simpan kategori baru
    // =============================================
    public function store(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'urutan'      => 'nullable|integer|min:0',
        ]);

        AssessmentCategory::create([
            'nama'        => $request->nama,
            'description' => $request->description,
            'urutan'      => $request->urutan ?? 0,
            'is_active'   => true,
        ]);

        return redirect()->route('admin.assessment-categories.index')
            ->with('success', 'Kategori berhasil ditambahkan!');
    }

    // =============================================
    // EDIT - Tampilkan form edit kategori
    // =============================================
    public function edit(AssessmentCategory $assessmentCategory)
    {
        return view('assessment.categories.form', [
            'category' => $assessmentCategory
        ]);
    }

    // =============================================
    // UPDATE - Simpan perubahan kategori
    // =============================================
    public function update(Request $request, AssessmentCategory $assessmentCategory)
    {
        $request->validate([
            'nama'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'urutan'      => 'nullable|integer|min:0',
        ]);

        $assessmentCategory->update([
            'nama'        => $request->nama,
            'description' => $request->description,
            'urutan'      => $request->urutan ?? $assessmentCategory->urutan,
        ]);

        return redirect()->route('admin.assessment-categories.index')
            ->with('success', 'Kategori berhasil diupdate!');
    }

    // =============================================
    // TOGGLE ACTIVE - Nonaktifkan / Aktifkan kategori
    // Pertanyaan di dalamnya ikut tersembunyi dari form
    // Data penilaian lama tetap AMAN tidak terhapus
    // =============================================
    public function toggleActive(AssessmentCategory $assessmentCategory)
    {
        $assessmentCategory->update([
            'is_active' => !$assessmentCategory->is_active
        ]);

        $status = $assessmentCategory->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Kategori berhasil {$status}!");
    }

    // =============================================
    // DESTROY - Hapus permanen
    // HATI-HATI: semua pertanyaan dalam kategori ini
    // dan semua data penilaian yang terkait
    // akan IKUT TERHAPUS (cascade delete)
    // =============================================
    public function destroy(AssessmentCategory $assessmentCategory)
    {
        $assessmentCategory->delete();

        return redirect()->back()
            ->with('success', 'Kategori berhasil dihapus permanen!');
    }
}
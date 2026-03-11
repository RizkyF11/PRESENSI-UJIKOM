<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentCategory extends Model
{
    protected $fillable = ['nama', 'description', 'urutan', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // =============================================
    // 1 kategori punya banyak pertanyaan
    // Ini relasi utama sekarang — bukan ke detail langsung
    // Urutan: tampil sesuai kolom 'urutan'
    // =============================================
    public function questions()
    {
        return $this->hasMany(AssessmentQuestion::class, 'category_id')
                    ->orderBy('urutan');
    }

    // =============================================
    // Sama seperti questions() tapi hanya yang aktif
    // Dipakai di form penilaian (manager tidak perlu
    // lihat pertanyaan yang sudah dinonaktifkan admin)
    // =============================================
    public function activeQuestions()
    {
        return $this->hasMany(AssessmentQuestion::class, 'category_id')
                    ->where('is_active', true)
                    ->orderBy('urutan');
    }

    // Scope: hanya ambil kategori yang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('urutan');
    }
}
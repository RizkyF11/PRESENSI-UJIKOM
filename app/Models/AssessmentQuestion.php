<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentQuestion extends Model
{
    protected $fillable = ['category_id', 'question', 'urutan', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // =============================================
    // Pertanyaan ini milik kategori mana
    // Contoh: pertanyaan "Apakah tepat waktu?" 
    // milik kategori "Kedisiplinan"
    // =============================================
    public function category()
    {
        return $this->belongsTo(AssessmentCategory::class, 'category_id');
    }

    // =============================================
    // 1 pertanyaan bisa dinilai di banyak sesi penilaian
    // (setiap karyawan dinilai setiap bulan = banyak sesi)
    // =============================================
    public function details()
    {
        return $this->hasMany(AssessmentDetail::class, 'question_id');
    }

    // Scope: hanya ambil pertanyaan yang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('urutan');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class AssessmentDetail extends Model
{
    protected $fillable = [
        'assessment_id',
        'question_id',  // ← pakai question_id, bukan category_id
        'score'
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    // =============================================
    // Detail ini milik sesi penilaian mana
    // =============================================
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    // =============================================
    // Detail ini untuk pertanyaan mana
    // Dari sini bisa akses kategorinya juga via:
    // $detail->question->category->nama
    // =============================================
    public function question()
    {
        return $this->belongsTo(AssessmentQuestion::class, 'question_id');
    }

    // =============================================
    // Helper: ubah angka score jadi teks label
    // Pakai: $detail->score_label
    // Contoh: score 4 → "Sangat Baik"
    // =============================================
    public function getScoreLabelAttribute(): string
    {
        return match($this->score) {
            1       => 'Kurang',
            2       => 'Cukup',
            3       => 'Baik',
            4       => 'Sangat Baik',
            5       => 'Istimewa',
            default => 'Belum dinilai',
        };
    }
}
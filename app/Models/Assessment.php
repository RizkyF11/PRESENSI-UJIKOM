<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = [
        'evaluator_id',
        'evaluatee_id',
        'assessment_date',
        'period',
        'general_notes'
    ];

    protected $casts = [
        'assessment_date' => 'date',
    ];

    // =============================================
    // Siapa yang menilai (manager/guru)
    // =============================================
    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    // =============================================
    // Siapa yang dinilai (karyawan)
    // =============================================
    public function evaluatee()
    {
        return $this->belongsTo(User::class, 'evaluatee_id');
    }

    // =============================================
    // 1 sesi penilaian punya banyak detail nilai
    // Setiap baris detail = 1 pertanyaan yang dinilai
    // =============================================
    public function details()
    {
        return $this->hasMany(AssessmentDetail::class);
    }

    // =============================================
    // Helper: hitung rata-rata semua pertanyaan
    // dalam sesi ini. Pakai: $assessment->rata_rata
    // =============================================
    public function getRataRataAttribute(): float
    {
        return round($this->details->avg('score') ?? 0, 2);
    }

    // =============================================
    // Helper: rata-rata per kategori dalam sesi ini
    // Pakai: $assessment->rata_rata_per_kategori
    // Return: ['Teamwork' => 4.2, 'Kedisiplinan' => 3.8]
    // =============================================
    public function getRataRataPerKategoriAttribute()
    {
        return $this->details
            ->groupBy(fn($d) => $d->question->category->nama)
            ->map(fn($group) => round($group->avg('score'), 2));
    }
}

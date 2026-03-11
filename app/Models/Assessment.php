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
        'assessment_date' => 'date', // Otomatis cast ke Carbon date
    ];

    // =============================================
    // Siapa yang menilai (relasi ke manager)
    // =============================================
    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    // =============================================
    // Siapa yang dinilai (relasi ke karyawan)
    // =============================================
    public function evaluatee()
    {
        return $this->belongsTo(User::class, 'evaluatee_id');
    }

    // =============================================
    // 1 sesi penilaian punya banyak detail nilai
    // =============================================
    public function details()
    {
        return $this->hasMany(AssessmentDetail::class);
    }
}
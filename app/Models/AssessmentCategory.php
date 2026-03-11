<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentCategory extends Model
{
    protected $fillable = ['nama', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean', // Otomatis cast ke true/false
    ];

    // =============================================
    // 1 kategori dipakai di banyak detail penilaian
    // =============================================
    public function assessmentDetails()
    {
        return $this->hasMany(AssessmentDetail::class, 'category_id');
    }

    // Scope: hanya ambil kategori yang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
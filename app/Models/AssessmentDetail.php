<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentDetail extends Model
{
    protected $fillable = ['assessment_id', 'category_id', 'score'];

    // =============================================
    // Detail ini milik sesi penilaian mana
    // =============================================
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    // =============================================
    // Detail ini untuk kategori apa
    // =============================================
    public function category()
    {
        return $this->belongsTo(AssessmentCategory::class, 'category_id');
    }
}
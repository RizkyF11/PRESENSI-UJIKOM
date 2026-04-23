<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SatisfactionRating extends Model
{
    protected $fillable = [
        'ticket_id',
        'reporter_id',
        'score',
        'feedback',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    public function ticket()
    {
        return $this->belongsTo(Tickets::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    // ==================== HELPERS ====================

    /**
     * Tampilkan score sebagai bintang (★)
     * Contoh: score 4 → "★★★★☆"
     */
    public function getStarsAttribute(): string
    {
        return str_repeat('★', $this->score) . str_repeat('☆', 5 - $this->score);
    }
}
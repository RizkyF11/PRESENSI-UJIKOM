<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketsResponse extends Model
{
    protected $table = 'ticket_responses';

    protected $fillable = [
        'ticket_id',
        'responder_id',
        'message',
        'is_auto_reply',
    ];

    protected $casts = [
        'is_auto_reply' => 'boolean',
    ];

    // ==================== RELATIONSHIPS ====================

    public function ticket()
    {
        return $this->belongsTo(Tickets::class);
    }

    public function responder()
    {
        return $this->belongsTo(User::class, 'responder_id');
    }

    // ==================== HELPERS ====================

    /**
     * Cek apakah balasan ini dikirim oleh admin/operator
     * (bukan dari pelapor/karyawan)
     */
    public function isFromOperator(): bool
    {
        return in_array($this->responder->role, ['admin', 'manager']);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Tickets extends Model
{
    protected $fillable = [
        'reporter_id',
        'operator_id',
        'subject',
        'description',
        'category',
        'priority',
        'status',
        'first_response_at',
        'resolved_at',
    ];

    protected $casts = [
        'first_response_at' => 'datetime',
        'resolved_at'       => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function responses()
    {
        return $this->hasMany(TicketsResponse::class, 'ticket_id');
    }

    public function rating()
    {
        return $this->hasOne(SatisfactionRating::class, 'ticket_id');
    }

    // ==================== SCOPES ====================

    /**
     * Urutkan tiket berdasarkan prioritas: High > Mid > Low,
     * lalu created_at terlama (yang paling lama menunggu duluan)
     */
    public function scopeByPriority(Builder $query): Builder
    {
        return $query
            ->orderByRaw("FIELD(priority, 'High', 'Mid', 'Low')")
            ->orderBy('created_at');
    }

    // ==================== FULL-TEXT SEARCH ====================

    /**
     * Cari tiket serupa berdasarkan subject & description
     * Digunakan untuk fitur Anti-Duplikasi sebelum pelapor submit tiket baru
     */
    public static function searchSimilar(string $keyword, int $excludeId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = self::whereRaw(
                'MATCH(subject, description) AGAINST(? IN BOOLEAN MODE)',
                [$keyword . '*']
            )
            ->whereNotIn('status', ['Closed'])
            ->with('reporter')
            ->limit(5);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->get();
    }

    // ==================== SLA HELPERS ====================

    /**
     * Hitung Response Time dalam menit
     * (dari created_at sampai first_response_at)
     * Null jika operator belum pernah reply
     */
    public function getResponseTimeMinutesAttribute(): ?int
    {
        if (!$this->first_response_at) return null;

        return (int) $this->created_at->diffInMinutes($this->first_response_at);
    }

    /**
     * Hitung Resolution Time dalam menit
     * (dari created_at sampai resolved_at / status Closed)
     * Null jika tiket belum selesai
     */
    public function getResolutionTimeMinutesAttribute(): ?int
    {
        if (!$this->resolved_at) return null;

        return (int) $this->created_at->diffInMinutes($this->resolved_at);
    }

    // ==================== AUTO-REPLY SUGGESTION ====================

    /**
     * Daftar saran jawaban otomatis berdasarkan kategori
     * Digunakan saat admin/operator akan membalas tiket
     */
    public static function getAutoReplySuggestions(): array
    {
        return [
            'jaringan' => 'Terima kasih sudah melaporkan. Tim kami sedang mengecek koneksi jaringan di area Anda. Mohon tunggu konfirmasi dalam 30 menit.',
            'hardware'  => 'Laporan kerusakan hardware sudah kami terima. Teknisi akan segera dikirim ke lokasi Anda. Harap pastikan perangkat tidak digunakan sementara.',
            'software'  => 'Kami sedang mengidentifikasi masalah pada aplikasi yang dilaporkan. Coba restart aplikasi terlebih dahulu sambil menunggu solusi dari tim kami.',
            'akses'     => 'Permintaan akses/izin Anda sedang diproses oleh tim IT. Estimasi selesai dalam 1x24 jam kerja.',
            'email'     => 'Masalah email Anda sudah tercatat. Pastikan koneksi internet stabil dan coba logout lalu login kembali ke akun email Anda.',
            'lainnya'   => 'Laporan Anda sudah kami terima dan sedang dalam proses penanganan. Kami akan segera menghubungi Anda dengan solusi.',
        ];
    }

    /**
     * Ambil saran jawaban untuk tiket ini berdasarkan kategorinya.
     * Bisa dipanggil dengan $ticket->auto_reply
     */
    public function getAutoReplyAttribute(): string
    {
        $suggestions = self::getAutoReplySuggestions();

        return $suggestions[$this->category] ?? $suggestions['lainnya'];
    }
}
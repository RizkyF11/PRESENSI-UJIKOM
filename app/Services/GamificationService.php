<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\FlexibilityItem;
use App\Models\PointLedger;
use App\Models\PointRule;
use App\Models\User;
use App\Models\UserToken;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    /*
    |--------------------------------------------------------------------------
    | MAIN FUNCTION
    |--------------------------------------------------------------------------
    | Fungsi utama yang dipanggil setiap selesai absensi berhasil dibuat.
    |
    | Flow:
    | 1. Ambil user dari data absensi.
    | 2. Cegah proses ganda / duplicate ledger.
    | 3. Cek apakah user punya token interceptor.
    | 4. Jika tidak ada token → evaluasi point rules.
    */
    public function evaluateAndRecord(Absensi $absensi): void
    {

        // Ambil user dari relasi karyawan
        $user = $absensi->karyawan?->user;

        // Jika user tidak ditemukan hentikan proses
        if (!$user) {
            return;
        }

        // kalau alpha dari scheduler
        if ($absensi->is_alpha_generated) {
            $this->evaluateAlpha($absensi);
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Anti double process
        |--------------------------------------------------------------------------
        | Mencegah ledger masuk 2x untuk absensi yang sama.
        */
        if (PointLedger::where('absensi_id', $absensi->id)->exists()) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Token interceptor
        |--------------------------------------------------------------------------
        | Jika user telat dan punya token maka token digunakan otomatis.
        */
        if ($this->interceptToken($absensi, $user)) {
            return;
        }

        if (!$absensi->jam_masuk || !$absensi->shift) {
            return;
        }

        // Jalankan evaluasi semua point rules
        try {
            $this->evaluateRules($absensi, $user);
        } catch (\Throwable $e) {
            throw new \Exception('Evaluasi rules gagal: ' . $e->getMessage());
        }
    }


    /*
    |--------------------------------------------------------------------------
    | RULE ENGINE
    |--------------------------------------------------------------------------
    | Mesin evaluasi rule point.
    |
    | Bertugas membaca seluruh rules dari database lalu mengecek:
    | - apakah user datang lebih awal?
    | - apakah user terlambat?
    |
    | Jika cocok dengan rule maka ledger akan dibuat.
    */
    /*
    |--------------------------------------------------------------------------
    | RULE ENGINE (UPGRADED)
    |--------------------------------------------------------------------------
    */
    private function evaluateRules(Absensi $absensi, User $user): void
    {
        // 1. ANTI MISKOM ROLE: Jadikan huruf kecil semua agar kebal huruf besar/kecil (Case Insensitive)
        $rules = PointRule::whereRaw('LOWER(target_role) = ?', [strtolower($user->role)])
            ->orderBy('point_modifier', 'asc')
            ->get();

        // Jika tidak ada rule untuk role tersebut, hentikan
        if ($rules->isEmpty()) {
            return;
        }

        $shift = $absensi->shift;
        if (!$shift) {
            return;
        }

        // 2. ANTI MISKOM JAM: Gunakan Carbon agar tanggal & jam ikut dihitung (Jangan pakai strtotime)
        $waktuShift = \Carbon\Carbon::parse($absensi->tanggal . ' ' . $shift->jam_masuk);
        $waktuMasuk = \Carbon\Carbon::parse($absensi->tanggal . ' ' . $absensi->jam_masuk);

        // Penyesuaian Jika Shift Lintas Malam (Misal: Shift 00:00, Absen 23:50)
        if ($waktuShift->format('H') < 12 && $waktuMasuk->format('H') >= 12) {
            $waktuShift->addDay();
        } elseif ($waktuShift->format('H') >= 12 && $waktuMasuk->format('H') < 12) {
            $waktuShift->subDay();
        }

        /*
        |--------------------------------------------------------------------------
        | Hitung EARLY & LATE Minutes yang Akurat
        |--------------------------------------------------------------------------
        */
        $earlyMinutes = 0;
        $lateMinutes = 0;

        if ($waktuMasuk->lessThan($waktuShift)) {
            // Hitung persis berapa menit dia datang lebih awal
            $earlyMinutes = $waktuMasuk->diffInMinutes($waktuShift);
        } elseif ($waktuMasuk->greaterThan($waktuShift)) {
            // Hitung keterlambatan
            $selisih = $waktuShift->diffInMinutes($waktuMasuk);
            $toleransi = $shift->toleransi_menit ?? 0;

            // Fix ✅ — jika selisih >= toleransi, berarti sudah terlambat
            if ($selisih >= $toleransi) {
                // Pakai selisih penuh, bukan dikurangi toleransi
                // karena rule LATE_MINUTES > 0 harus match
                $lateMinutes = $selisih;
            }
        }

        $penaltyApplied = false;



        // Loop seluruh rule satu-persatu
        foreach ($rules as $rule) {

            $value = 0;

            switch ($rule->conditional_type) {
                case 'EARLY_MINUTES':
                    $value = $earlyMinutes;
                    break;
                case 'LATE_MINUTES':
                    $value = $lateMinutes;
                    break;
                default:
                    continue 2;
            }

            // 3. Evaluasi Kondisi
            $match = $this->evaluateCondition(
                $value,
                $rule->condition_operator,
                $rule->condition_value
            );

            // Jika tidak match lanjut rule berikutnya
            if (!$match) {
                continue;
            }

            $type = $rule->point_modifier > 0 ? 'EARN' : 'PENALTY';

            if ($type === 'PENALTY' && $penaltyApplied) {
                continue;
            }

            // SIMPAN POIN KE LEDGER
            $this->recordLedger(
                user: $user,
                type: $type,
                amount: abs($rule->point_modifier),
                description: "Rule[{$rule->rule_name}] - Absensi {$absensi->tanggal}",
                absensi: $absensi
            );

            if ($type === 'PENALTY') {
                $penaltyApplied = true;
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | EVALUATE CONDITION
    |--------------------------------------------------------------------------
    | Mengecek operator rule:
    |
    | <       = lebih kecil dari
    | >       = lebih besar dari
    | BETWEEN = diantara range
    */
    private function evaluateCondition(
        int $value,
        string $operator,
        string $conditionValue
    ): bool {

        switch ($operator) {

            case '<':
                return $value < (int)$conditionValue;

            case '>':
                return $value > (int)$conditionValue;

            case 'BETWEEN':

                // Validasi format harus ada koma
                if (!str_contains($conditionValue, ',')) {
                    return false;
                }

                // Pecah range from,to
                [$from, $to] = explode(',', $conditionValue);

                return
                    $value >= (int)trim($from) &&
                    $value <= (int)trim($to);
        }

        return false;
    }


    /*
    |--------------------------------------------------------------------------
    | RECORD LEDGER
    |--------------------------------------------------------------------------
    | Menyimpan histori perubahan point ke tabel ledger.
    |
    | Semua transaksi point masuk sini:
    | - Reward
    | - Penalty
    | - Spend Token
    */
    public function recordLedger(
        User $user,
        string $type,
        int $amount,
        string $description,
        ?Absensi $absensi = null,
        ?UserToken $userToken = null
    ): PointLedger {

        return DB::transaction(function () use (
            $user,
            $type,
            $amount,
            $description,
            $absensi,
            $userToken
        ) {

            // Ambil ledger terakhir untuk saldo sekarang
            $lastLedger = PointLedger::where('user_id', $user->id)
                ->latest()
                ->lockForUpdate()
                ->first();

            $currentBalance =
                $lastLedger?->current_balance ?? 0;

            /*
            |--------------------------------------------------------------------------
            | Hitung saldo baru
            |--------------------------------------------------------------------------
            */
            $newBalance = match ($type) {
                'EARN' => $currentBalance + $amount,
                'PENALTY' => max(0, $currentBalance - $amount),
                'SPEND' => max(0, $currentBalance - $amount),
            };

            /*
            |--------------------------------------------------------------------------
            | Insert ledger baru
            |--------------------------------------------------------------------------
            */
            return PointLedger::create([
                'user_id' => $user->id,
                'transaction_type' => $type,
                'amount' => $amount,
                'current_balance' => $newBalance,
                'description' => $description,
                'absensi_id' => $absensi?->id,
                'user_token_id' => $userToken?->id,
            ]);
        });
    }


    /*
    |--------------------------------------------------------------------------
    | TOKEN INTERCEPTOR
    |--------------------------------------------------------------------------
    | Jika user terlambat dan punya token available:
    | maka token dipakai otomatis.
    */
    private function interceptToken(Absensi $absensi, User $user): bool
    {
        // Jika status bukan terlambat skip
        if ($absensi->status_masuk !== 'terlambat') {
            return false;
        }

        // Cari token available paling lama
        $token = UserToken::where('user_id', $user->id)
            ->where('status', 'AVAILABLE')
            ->oldest()
            ->first();

        // Jika tidak ada token
        if (!$token) {
            return false;
        }

        DB::transaction(function () use ($token, $absensi) {

            // Pakai token
            $token->update([
                'status' => 'USED',
                'used_at_absensi_id' => $absensi->id,
            ]);
        });

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | GET BALANCE
    |--------------------------------------------------------------------------
    | Mengambil saldo point terbaru user.
    */
    public function getCurrentBalance(User $user): int
    {
        return PointLedger::where('user_id', $user->id)
            ->latest()
            ->value('current_balance') ?? 0;
    }


    /*
    |--------------------------------------------------------------------------
    | REDEEM TOKEN
    |--------------------------------------------------------------------------
    | Menukar point menjadi token.
    */
    public function redeemToken(User $user, FlexibilityItem $item): array
    {
        if ($this->getCurrentBalance($user) < $item->point_cost) {
            return ['success' => false, 'message' => 'Saldo poin tidak cukup.'];
        }

        // recordLedger sudah pakai DB::transaction() sendiri, tidak perlu wrap lagi
        $ledger = $this->recordLedger(
            user: $user,
            type: 'SPEND',
            amount: $item->point_cost,
            description: "Redeem token {$item->item_name}"
        );

        $token = UserToken::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'status'  => 'AVAILABLE',
        ]);

        $ledger->update(['user_token_id' => $token->id]);

        return ['success' => true, 'message' => 'Token berhasil ditukar.'];
    }


    public function evaluateAlpha(Absensi $absensi): void
    {
        $user = $absensi->karyawan?->user;

        if (!$user) return;

        // prevent double alpha only
        if (PointLedger::where('absensi_id', $absensi->id)
            ->where('transaction_type', 'PENALTY')
            ->where('description', 'Alpha')
            ->exists()
        ) {
            return;
        }

        $rules = PointRule::where('target_role', $user->role)
            ->where('conditional_type', 'ALPHA')
            ->get();

        if ($rules->isEmpty()) {
            $this->recordLedger(
                user: $user,
                type: 'PENALTY',
                amount: 10,
                description: "Alpha default penalty - {$absensi->tanggal}",
                absensi: $absensi
            );
            return;
        }

        foreach ($rules as $rule) {
            $this->recordLedger(
                user: $user,
                type: 'PENALTY',
                amount: abs($rule->point_modifier),
                description: "Alpha penalty - {$absensi->tanggal}",
                absensi: $absensi
            );
        }
    }
}

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
        $user = $absensi->karyawan->user;

        // Jika user tidak ditemukan hentikan proses
        if (!$user) {
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

        // Jalankan evaluasi semua point rules
        $this->evaluateRules($absensi, $user);
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
    private function evaluateRules(Absensi $absensi, User $user): void
    {
        // Ambil semua rule sesuai role user
        $rules = PointRule::where('target_role', $user->role)
            ->orderBy('point_modifier', 'asc')
            ->get();

        // Jika tidak ada rule maka stop
        if ($rules->isEmpty()) {
            return;
        }

        // Ambil shift dari absensi
        $shift = $absensi->shift;

        // Jika shift kosong stop
        if (!$shift) {
            return;
        }

        // Convert jam masuk dan jam shift ke timestamp
        $jamMasuk = strtotime($absensi->jam_masuk);
        $jamShift = strtotime($shift->jam_masuk);

        /*
        |--------------------------------------------------------------------------
        | Hitung EARLY & LATE Minutes
        |--------------------------------------------------------------------------
        | Menghitung:
        | - berapa menit user datang lebih awal
        | - berapa menit user terlambat
        */
        $earlyMinutes = 0;
        $lateMinutes = 0;

        // Jika datang sebelum shift = early
        if ($jamMasuk < $jamShift) {
            $earlyMinutes = floor(($jamShift - $jamMasuk) / 60);
        }

        // Jika datang setelah shift = potential late
        if ($jamMasuk > $jamShift) {

            $selisih = floor(($jamMasuk - $jamShift) / 60);

            // Jika melebihi toleransi shift baru dianggap telat
            if ($selisih > $shift->toleransi_menit) {
                $lateMinutes = $selisih;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Anti double penalty
        |--------------------------------------------------------------------------
        | Mencegah lebih dari 1 penalty dalam 1 absensi.
        */
        $penaltyApplied = false;

        // Loop seluruh rule satu-persatu
        foreach ($rules as $rule) {

            $value = 0;

            /*
            |--------------------------------------------------------------------------
            | Mapping nilai evaluasi berdasarkan conditional type
            |--------------------------------------------------------------------------
            */
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

            /*
            |--------------------------------------------------------------------------
            | Evaluasi apakah kondisi rule match
            |--------------------------------------------------------------------------
            */
            $match = $this->evaluateCondition(
                $value,
                $rule->condition_operator,
                $rule->condition_value
            );

            // Jika tidak match lanjut rule berikutnya
            if (!$match) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Tentukan jenis transaksi ledger
            |--------------------------------------------------------------------------
            */
            $type =
                $rule->point_modifier > 0
                ? 'EARN'
                : 'PENALTY';

            /*
            |--------------------------------------------------------------------------
            | Prevent double penalty
            |--------------------------------------------------------------------------
            | Jika penalty sudah pernah diterapkan skip penalty berikutnya.
            */
            if ($type === 'PENALTY' && $penaltyApplied) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Simpan ledger transaksi point
            |--------------------------------------------------------------------------
            */
            $this->recordLedger(
                user: $user,
                type: $type,
                amount: abs($rule->point_modifier),
                description:
                    "Rule [{$rule->rule_name}] - Absensi {$absensi->tanggal}",
                absensi: $absensi
            );

            // Tandai bahwa penalty sudah dipakai
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
        if ($absensi->status_masuk !== 'TERLAMBAT') {
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

            // Ubah status absensi
            $absensi->update([
                'status_masuk' => 'TOKEN_USED'
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
        // Cek apakah saldo cukup
        if ($this->getCurrentBalance($user) < $item->point_cost) {

            return [
                'success' => false,
                'message' => 'Saldo poin tidak cukup.'
            ];
        }

        DB::transaction(function () use ($user, $item) {

            // Potong point user
            $ledger = $this->recordLedger(
                user: $user,
                type: 'SPEND',
                amount: $item->point_cost,
                description: "Redeem token {$item->item_name}"
            );

            // Tambahkan token baru
            $token = UserToken::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'status' => 'AVAILABLE',
            ]);

            // Update relasi ledger ke token
            $ledger->update([
                'user_token_id' => $token->id
            ]);
        });

        return [
            'success' => true,
            'message' => 'Token berhasil ditukar.'
        ];
    }
}
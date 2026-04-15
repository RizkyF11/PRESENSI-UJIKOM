<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointLedger extends Model
{
    protected $table = 'point_ledgers';

    protected $fillable = [
        'user_id',
        'transaction_type',
        'amount',
        'current_balance',
        'description',
        'absensi_id',       // TAMBAHAN
        'user_token_id',    // TAMBAHAN
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION TO USER
    |--------------------------------------------------------------------------
    | Setiap mutasi poin dimiliki oleh 1 user
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | RELATION TO ABSENSI
    |--------------------------------------------------------------------------
    | Jika poin didapat/dikurangi karena absensi, catat absensi mana
    | Nullable — tidak semua ledger berasal dari absensi (bisa dari SPEND)
    */
    public function absensi()
    {
        return $this->belongsTo(Absensi::class);
    }

    /*
    |--------------------------------------------------------------------------
    | RELATION TO USER TOKEN
    |--------------------------------------------------------------------------
    | Jika poin berkurang karena beli token, catat token mana yang dibeli
    | Nullable — tidak semua ledger berasal dari pembelian token
    */
    public function userToken()
    {
        return $this->belongsTo(UserToken::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER: Apakah transaksi ini menambah atau mengurangi poin?
    |--------------------------------------------------------------------------
    */
    public function isCredit(): bool
    {
        return $this->transaction_type === 'EARN';
    }

    public function isDebit(): bool
    {
        return in_array($this->transaction_type, ['SPEND', 'PENALTY']);
    }
}
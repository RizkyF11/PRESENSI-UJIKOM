<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserToken extends Model
{
    protected $table = 'user_tokens';

    protected $fillable = [
        'user_id',
        'item_id',
        'status',
        'used_at_absensi_id'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION TO USER
    |--------------------------------------------------------------------------
    | 1 Token dimiliki oleh 1 User
    |
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | RELATION TO FLEXIBILITY ITEM
    |--------------------------------------------------------------------------
    | Token berasal dari item marketplace tertentu
    |
    */
    public function item()
    {
        return $this->belongsTo(FlexibilityItem::class, 'item_id');
    }

    /*
    |--------------------------------------------------------------------------
    | RELATION TO ABSENSI
    |--------------------------------------------------------------------------
    | Jika token digunakan, maka tercatat digunakan pada absensi tertentu
    |
    */
    public function absensi()
    {
        return $this->belongsTo(Absensi::class, 'used_at_absensi_id');
    }
}
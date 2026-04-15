<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlexibilityItem extends Model
{
    protected $table = 'flexibility_items';

    protected $fillable = [
        'item_name',
        'point_cost',
        'stock_limit',
        'is_active',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION TO USER TOKENS
    |--------------------------------------------------------------------------
    | 1 Item bisa dimiliki banyak user dalam bentuk token
    |
    */
    public function userTokens()
    {
        return $this->hasMany(UserToken::class, 'item_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointRule extends Model
{
    protected $table = 'point_rules';

    protected $fillable = [
        'rule_name',
        'target_role',
        'conditional_type',
        'condition_operator',
        'condition_value',
        'point_modifier'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    | Tidak memiliki relasi langsung.
    | Karena tabel ini hanya dipakai sebagai konfigurasi rule oleh sistem.
    |
    */
}
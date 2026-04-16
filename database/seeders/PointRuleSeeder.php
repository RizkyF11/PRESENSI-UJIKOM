<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PointRuleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('point_rules')->insert([

            /*
            |------------------------------------------------------------------
            | EARLY MINUTES — Datang Lebih Awal
            |------------------------------------------------------------------
            | Menggunakan conditional_type: EARLY_MINUTES
            | Dihitung dari berapa menit sebelum jam shift masuk
            */

            [
                'rule_name'          => 'Datang Super Awal',
                'target_role'        => 'karyawan',
                'conditional_type'   => 'EARLY_MINUTES',
                'condition_operator' => '>',
                'condition_value'    => '30',
                // Jika datang lebih dari 30 menit sebelum shift → +10 poin
                'point_modifier'     => 10,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],

            [
                'rule_name'          => 'Datang Lebih Awal',
                'target_role'        => 'karyawan',
                'conditional_type'   => 'EARLY_MINUTES',
                'condition_operator' => 'BETWEEN',
                'condition_value'    => '10,30',
                // Jika datang 10-30 menit sebelum shift → +5 poin
                'point_modifier'     => 5,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],

            [
                'rule_name'          => 'Datang Tepat Waktu',
                'target_role'        => 'karyawan',
                'conditional_type'   => 'EARLY_MINUTES',
                'condition_operator' => 'BETWEEN',
                'condition_value'    => '0,9',
                // Jika datang 0-9 menit sebelum shift → +3 poin
                'point_modifier'     => 3,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],

            /*
            |------------------------------------------------------------------
            | LATE MINUTES — Terlambat
            |------------------------------------------------------------------
            | Menggunakan conditional_type: LATE_MINUTES
            | Dihitung dari berapa menit setelah toleransi shift habis
            */

            [
                'rule_name'          => 'Terlambat Ringan',
                'target_role'        => 'karyawan',
                'conditional_type'   => 'LATE_MINUTES',
                'condition_operator' => 'BETWEEN',
                'condition_value'    => '1,15',
                // Terlambat 1-15 menit → -3 poin
                'point_modifier'     => -3,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],

            [
                'rule_name'          => 'Terlambat Sedang',
                'target_role'        => 'karyawan',
                'conditional_type'   => 'LATE_MINUTES',
                'condition_operator' => 'BETWEEN',
                'condition_value'    => '16,30',
                // Terlambat 16-30 menit → -5 poin
                'point_modifier'     => -5,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],

            [
                'rule_name'          => 'Terlambat Parah',
                'target_role'        => 'karyawan',
                'conditional_type'   => 'LATE_MINUTES',
                'condition_operator' => '>',
                'condition_value'    => '30',
                // Terlambat lebih dari 30 menit → -10 poin
                'point_modifier'     => -10,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],

            /*
            |------------------------------------------------------------------
            | ALPHA — Tidak Masuk Tanpa Keterangan
            |------------------------------------------------------------------
            */
            [
                'rule_name'          => 'Alpha',
                'target_role'        => 'karyawan',
                'conditional_type'   => 'ALPHA',
                'condition_operator' => '>',
                'condition_value'    => '0',
                // Alpha → -15 poin
                'point_modifier'     => -15,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
        ]);
    }
}
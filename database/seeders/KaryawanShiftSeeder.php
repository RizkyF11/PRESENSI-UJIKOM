<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Karyawan;
use App\Models\Shift;

class KaryawanShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shiftPagi = Shift::where('nama_shift', 'Pagi')->first();

        if (!$shiftPagi) return;

        foreach (Karyawan::all() as $karyawan) {

            DB::table('karyawan_shift')->updateOrInsert(
                [
                    'karyawan_id' => $karyawan->id,
                    'shift_id' => $shiftPagi->id
                ],
                [
                    'tanggal_mulai' => Carbon::today()->subYear(),
                    'tanggal_selesai' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
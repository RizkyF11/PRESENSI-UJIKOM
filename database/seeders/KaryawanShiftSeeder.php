<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\{Karyawan, Shift};

class KaryawanShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shiftPagi = Shift::where('nama_shift', 'Pagi')->first();

        if (! $shiftPagi) return;

        Karyawan::all()->each(function ($karyawan) use ($shiftPagi) {
            // Cek apakah sudah punya relasi shift
            $sudahAda = DB::table('karyawan_shift')
                ->where('karyawan_id', $karyawan->id)
                ->where('shift_id', $shiftPagi->id)
                ->exists();

            if (! $sudahAda) {
                DB::table('karyawan_shift')->insert([
                    'karyawan_id'    => $karyawan->id,
                    'shift_id'       => $shiftPagi->id,
                    'tanggal_mulai'  => Carbon::now()->subYear()->toDateString(),
                    'tanggal_selesai'=> null,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        });
    }
}
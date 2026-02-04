<?php

namespace Database\Seeders;

use App\Models\Cuti;
use App\Models\Karyawan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CutiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $karyawans = Karyawan::all();

        foreach ($karyawans as $karyawan) {
            //tidak semua karyawan harus punya cuti
            if (rand(1, 3)) {
                Cuti::factory()->create([
                    'karyawan_id' => $karyawan->id,
                ]);
            }
        }
    }
}

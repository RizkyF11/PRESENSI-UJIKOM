<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LokasiKantor;

class LokasiKantorSeeder extends Seeder
{
    public function run(): void
    {
        LokasiKantor::updateOrCreate(
            ['nama_lokasi' => 'Kantor Pusat'],
            [
                'latitude'  => -6.200000,
                'longitude' => 106.816666,
                'radius'    => 500,
                'is_active' => true,
            ]
        );
    }
}
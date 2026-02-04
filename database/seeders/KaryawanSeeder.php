<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Database\Seeder;

class KaryawanSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'karyawan')->get();

        foreach ($users as $user) {
            Karyawan::factory()->create([
                'user_id' => $user->id,
            ]);
        }
    }
}

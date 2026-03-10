<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{User, Karyawan};

class KaryawanSeeder extends Seeder
{
    public function run(): void
    {
        $jabatans = [
            'Staff IT', 'Staff HR', 'Staff Keuangan',
            'Staff Marketing', 'Staff Operasional',
            'Supervisor IT', 'Supervisor HR', 'Manager IT',
            'Manager Keuangan', 'Staff Administrasi',
        ];

        $users = User::where('role', 'karyawan')->get();

        foreach ($users as $index => $user) {
            // Skip jika sudah punya data karyawan
            if ($user->karyawan()->exists()) continue;

            $no = $index + 1;

            Karyawan::create([
                'user_id' => $user->id,
                'nip'     => 'EMP' . str_pad($no, 4, '0', STR_PAD_LEFT),
                'jabatan' => $jabatans[$index % count($jabatans)],
                'no_hp'   => '0812' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                'alamat'  => 'Jl. Contoh No. ' . $no . ', Jakarta',
                'status'  => 'aktif',
            ]);
        }
    }
}
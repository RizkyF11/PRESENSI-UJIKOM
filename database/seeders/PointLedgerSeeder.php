<?php

namespace Database\Seeders;

use App\Models\PointLedger;
use App\Models\User;
use Illuminate\Database\Seeder;

class PointLedgerSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil hanya karyawan dari UserSeeder kamu
        $users = User::where('role', 'karyawan')
            ->where('email', 'like', 'karyawan%@gmail.com')
            ->get();

        foreach ($users as $index => $user) {

            /*
            |--------------------------------------------------------------------------
            | Set base score biar leaderboard tidak random 100% chaos
            |--------------------------------------------------------------------------
            | Karyawan 1 = paling bagus
            | Karyawan 10 = paling rendah
            */
            $baseScore = (10 - $index) * 10; // 90 - 0

            $balance = $baseScore;

            // bikin histori 10 transaksi
            for ($i = 1; $i <= 10; $i++) {

                // lebih sering EARN daripada PENALTY
                $type = fake()->randomElement([
                    'EARN',
                    'EARN',
                    'EARN',
                    'PENALTY'
                ]);

                $amount = rand(5, 15);

                if ($type === 'EARN') {
                    $balance += $amount;
                } else {
                    $balance -= $amount;
                }

                if ($balance < 0) {
                    $balance = 0;
                }

                PointLedger::create([
                    'user_id' => $user->id,
                    'transaction_type' => $type,
                    'amount' => $amount,
                    'current_balance' => $balance,
                    'description' => "Seeder transaksi {$user->nama}",
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }
    }
}
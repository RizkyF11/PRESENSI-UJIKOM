<?php

namespace Database\Seeders;

use App\Models\PointLedger;
use App\Models\User;
use Illuminate\Database\Seeder;

class PointLedgerSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereIn('role', ['karyawan', 'manager'])->get();

        foreach ($users as $user) {

            $balance = 0;

            for ($i = 1; $i <= 10; $i++) {

                $type = fake()->randomElement([
                    'EARN',
                    'PENALTY'
                ]);

                $amount = rand(5, 20);

                if ($type == 'EARN') {
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
                    'description' => 'Seeder Dummy Leaderboard'
                ]);
            }
        }
    }
}
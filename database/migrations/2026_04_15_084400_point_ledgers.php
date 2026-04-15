<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: point_ledgers (Mutasi Poin ala Bank)
     * Setiap perubahan poin WAJIB tercatat di sini — tidak boleh langsung UPDATE saldo.
     */
    public function up(): void
    {
        Schema::create('point_ledgers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('transaction_type', [
                'EARN',    // Dapat poin dari absen tepat waktu / early
                'SPEND',   // Kurangi poin karena beli token di marketplace
                'PENALTY'  // Kurangi poin karena terlambat / alpa
            ]);

            $table->integer('amount');
            // Selalu positif. Tanda +/- ditentukan dari transaction_type.
            // EARN = +amount, SPEND/PENALTY = -amount

            $table->integer('current_balance');
            // Saldo SETELAH transaksi ini — untuk audit trail

            $table->text('description');
            // Cth: "Datang tepat waktu 12/08/2024", "Beli Token Telat 30 Menit"

            // -------------------------------------------------------
            // TAMBAHAN: Traceability — poin ini dari/untuk transaksi mana?
            // -------------------------------------------------------

            $table->foreignId('absensi_id')
                ->nullable()
                ->constrained('absensi')
                ->nullOnDelete();
            // Diisi jika transaction_type = EARN atau PENALTY (dari absensi)
            // Null jika transaction_type = SPEND (dari marketplace)

            $table->foreignId('user_token_id')
                ->nullable()
                ->constrained('user_tokens')
                ->nullOnDelete();
            // Diisi jika transaction_type = SPEND (token apa yang dibeli)
            // Null jika transaction_type = EARN atau PENALTY

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_ledgers');
    }
};

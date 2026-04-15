<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: point_rules (Mesin Logika)
     * Berisi aturan dinamis yang dibuat admin untuk pemberian/pengurangan poin.
     * Contoh: JIKA Check-in < 06:30 MAKA +5 Poin
     */
    public function up(): void
    {
        Schema::create('point_rules', function (Blueprint $table) {
            $table->id();

            $table->string('rule_name');
            // Contoh: "Datang Pagi Banget", "Terlambat 15 Menit"

            $table->string('target_role');
            // Berlaku untuk karyawan

            $table->enum('conditional_type', [
                'EARLY_MINUTES',
                'LATE_MINUTES',
                'ALPHA',
            ]);

            $table->enum('condition_operator', ['<', '>', 'BETWEEN']);
            // Operator kondisi waktu/nilai

            $table->string('condition_value');
            /*
            contoh:
            06:30:00
            15
            06:00:00,06:30:00
             */

            $table->integer('point_modifier');
            // Jumlah poin yang ditambah (+) atau dikurangi (-)
            // Contoh: +5 atau -3

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_rules');
    }
};

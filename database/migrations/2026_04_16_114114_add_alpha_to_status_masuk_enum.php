<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan value 'alpha' ke enum status_masuk pada tabel absensi.
     * Ini diperlukan agar command GenerateAlpha bisa menyimpan
     * data alpha ke database tanpa error constraint.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE absensi MODIFY COLUMN status_masuk ENUM('hadir', 'terlambat', 'alpha') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE absensi MODIFY COLUMN status_masuk ENUM('hadir', 'terlambat') NULL");
    }
};

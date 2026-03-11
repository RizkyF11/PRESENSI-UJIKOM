<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::create('assessment_details', function (Blueprint $table) {
            $table->id();

            // FK ke assessments: detail ini milik sesi penilaian mana
            $table->foreignId('assessment_id')
                  ->constrained('assessments')
                  ->onDelete('cascade');

            // FK ke assessment_categories: kategori apa yang dinilai
            $table->foreignId('category_id')
                  ->constrained('assessment_categories')
                  ->onDelete('cascade');

            $table->unsignedTinyInteger('score'); // Nilai 1-5
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_details');
    }
};

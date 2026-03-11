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

            // FK ke assessment_questions: pertanyaan apa yang dinilai
            $table->foreignId('question_id')
                ->constrained('assessment_questions')
                ->onDelete('cascade');

            $table->unsignedTinyInteger('score'); // Nilai 1-5
            $table->timestamps();

            // Satu pertanyaan hanya boleh dinilai 1x dalam 1 sesi
            $table->unique(['assessment_id', 'question_id']);
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

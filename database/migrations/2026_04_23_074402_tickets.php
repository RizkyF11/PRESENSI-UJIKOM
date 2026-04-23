<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('operator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('subject');
            $table->text('description');
            $table->string('category'); // Untuk auto-reply suggestion
            $table->enum('priority', ['Low', 'Mid', 'High'])->default('Low');
            $table->enum('status', ['Open', 'In-Progress', 'Closed'])->default('Open');
            $table->timestamp('first_response_at')->nullable(); // Untuk SLA Response Time
            $table->timestamp('resolved_at')->nullable();       // Untuk SLA Resolution Time
            $table->timestamps();

            // Full-Text Search index pada subject & description
            $table->fullText(['subject', 'description']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
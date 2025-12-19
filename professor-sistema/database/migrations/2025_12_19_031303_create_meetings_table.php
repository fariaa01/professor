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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('room_id')->unique(); // Identificador único da sala
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Professor
            $table->foreignId('aluno_id')->nullable()->constrained('alunos')->onDelete('cascade'); // Aluno opcional
            $table->foreignId('aula_id')->nullable()->constrained('aulas')->onDelete('set null'); // Vinculação com aula
            $table->string('title'); // Título da reunião
            $table->text('description')->nullable(); // Descrição
            $table->dateTime('scheduled_at')->nullable(); // Data/hora agendada
            $table->dateTime('started_at')->nullable(); // Quando iniciou
            $table->dateTime('ended_at')->nullable(); // Quando encerrou
            $table->enum('status', ['agendada', 'em_andamento', 'encerrada', 'cancelada'])->default('agendada');
            $table->integer('duration_minutes')->nullable(); // Duração em minutos
            $table->boolean('is_active')->default(true);
            $table->json('participants')->nullable(); // JSON com dados dos participantes que entraram
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['aluno_id', 'status']);
            $table->index('room_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};

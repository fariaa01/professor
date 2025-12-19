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
        Schema::create('horarios_aulas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aluno_id')->constrained('alunos')->onDelete('cascade');
            $table->tinyInteger('dia_semana'); // 0=domingo, 1=segunda, ..., 6=sábado
            $table->time('hora_inicio');
            $table->time('hora_fim');
            $table->integer('duracao_minutos');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            
            $table->index(['aluno_id', 'ativo']);
            $table->index(['dia_semana', 'ativo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios_aulas');
    }
};

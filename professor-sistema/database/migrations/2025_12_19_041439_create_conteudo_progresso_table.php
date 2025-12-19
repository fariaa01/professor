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
        Schema::create('conteudo_progresso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conteudo_id')->constrained('conteudos')->onDelete('cascade');
            $table->foreignId('aluno_id')->constrained('alunos')->onDelete('cascade');
            $table->timestamp('iniciado_em')->nullable();
            $table->timestamp('concluido_em')->nullable();
            $table->integer('progresso_segundos')->default(0); // Último segundo assistido
            $table->boolean('completo')->default(false);
            $table->integer('visualizacoes')->default(0); // Quantas vezes acessou
            $table->timestamps();
            
            $table->unique(['conteudo_id', 'aluno_id']);
            $table->index('aluno_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conteudo_progresso');
    }
};

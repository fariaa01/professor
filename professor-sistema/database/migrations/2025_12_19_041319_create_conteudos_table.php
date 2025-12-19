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
        Schema::create('conteudos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Professor
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->enum('tipo', ['video', 'pdf', 'texto', 'link'])->default('video');
            $table->string('url')->nullable(); // URL do vídeo/link
            $table->integer('duracao_segundos')->nullable(); // Duração do vídeo
            $table->json('materiais')->nullable(); // PDFs, imagens anexas
            $table->text('observacoes')->nullable(); // Notas do professor
            $table->enum('status', ['rascunho', 'publicado', 'arquivado'])->default('publicado');
            $table->json('alunos_ids')->nullable(); // IDs dos alunos com acesso
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conteudos');
    }
};

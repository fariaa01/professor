<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensagens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('aluno_id')->constrained('alunos')->onDelete('cascade');
            $table->enum('remetente', ['professor', 'aluno']);
            $table->text('mensagem');
            $table->boolean('lida')->default(false);
            $table->timestamps();
            
            $table->index(['professor_id', 'aluno_id', 'created_at']);
            $table->index(['aluno_id', 'lida']);
            $table->index(['professor_id', 'lida']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensagens');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('professor_aluno', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('professor_id');
            $table->unsignedBigInteger('aluno_id');
            $table->enum('status', ['pendente','ativo','bloqueado'])->default('pendente');
            $table->timestamps();

            $table->foreign('professor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('aluno_id')->references('id')->on('alunos')->onDelete('cascade');
            $table->unique(['professor_id','aluno_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professor_aluno');
    }
};

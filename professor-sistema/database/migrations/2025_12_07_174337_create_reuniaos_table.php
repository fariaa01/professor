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
        Schema::create('reuniaos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('aluno_id')->nullable()->constrained()->onDelete('cascade');
            $table->dateTime('data_hora');
            $table->integer('duracao_minutos')->default(60);
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->enum('status', ['agendada', 'realizada', 'cancelada'])->default('agendada');
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reuniaos');
    }
};

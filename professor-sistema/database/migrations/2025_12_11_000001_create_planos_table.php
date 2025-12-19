<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aluno_id')->constrained()->onDelete('cascade');
            $table->enum('tipo_plano', ['por_aula', 'pacote', 'mensalidade']);
            $table->decimal('valor_aula', 10, 2)->nullable()->comment('Valor unitário por aula (para tipo por_aula)');
            $table->decimal('valor_total', 10, 2)->nullable()->comment('Valor total do pacote/mensalidade');
            $table->integer('quantidade_aulas')->nullable()->comment('Quantidade de aulas no pacote');
            $table->date('data_inicio');
            $table->date('data_fim')->nullable()->comment('Data final do plano (para pacotes e mensalidades)');
            $table->boolean('ativo')->default(true);
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planos');
    }
};

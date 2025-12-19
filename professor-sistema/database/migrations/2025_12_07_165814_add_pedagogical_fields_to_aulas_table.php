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
        Schema::table('aulas', function (Blueprint $table) {
            $table->text('materiais')->nullable()->after('conteudo');
            $table->text('exercicios')->nullable()->after('materiais');
            $table->text('dificuldades')->nullable()->after('exercicios');
            $table->text('pontos_atencao')->nullable()->after('dificuldades');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aulas', function (Blueprint $table) {
            $table->dropColumn(['materiais', 'exercicios', 'dificuldades', 'pontos_atencao']);
        });
    }
};

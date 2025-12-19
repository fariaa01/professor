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
        Schema::table('alunos', function (Blueprint $table) {
            $table->decimal('valor_aula', 10, 2)->nullable()->after('telefone');
            $table->string('endereco')->nullable()->after('telefone');
            $table->string('responsavel')->nullable()->after('endereco');
            $table->string('telefone_responsavel')->nullable()->after('responsavel');
            $table->date('data_inicio')->nullable()->after('telefone_responsavel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alunos', function (Blueprint $table) {
            $table->dropColumn(['valor_aula', 'endereco', 'responsavel', 'telefone_responsavel', 'data_inicio']);
        });
    }
};

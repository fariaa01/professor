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
            $table->decimal('valor', 10, 2)->nullable()->after('duracao_minutos');
            $table->enum('forma_pagamento', ['dinheiro', 'pix', 'cartao', 'transferencia'])->nullable()->after('valor');
            $table->enum('status_pagamento', ['pendente', 'pago', 'atrasado'])->default('pendente')->after('forma_pagamento');
            $table->text('conteudo')->nullable()->after('observacoes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aulas', function (Blueprint $table) {
            $table->dropColumn(['valor', 'forma_pagamento', 'status_pagamento', 'conteudo']);
        });
    }
};

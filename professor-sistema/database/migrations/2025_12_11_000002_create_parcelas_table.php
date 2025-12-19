<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parcelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plano_id')->constrained()->onDelete('cascade');
            $table->integer('numero_parcela')->comment('Número da parcela (ex: 1, 2, 3...)');
            $table->integer('total_parcelas')->comment('Total de parcelas do plano (ex: 4, 12...)');
            $table->decimal('valor', 10, 2);
            $table->date('data_vencimento');
            $table->date('data_pagamento')->nullable();
            $table->enum('status_pagamento', ['pendente', 'pago', 'atrasado'])->default('pendente');
            $table->enum('forma_pagamento', ['dinheiro', 'pix', 'transferencia', 'cartao'])->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parcelas');
    }
};

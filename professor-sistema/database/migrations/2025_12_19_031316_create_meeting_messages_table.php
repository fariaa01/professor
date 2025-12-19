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
        Schema::create('meeting_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->onDelete('cascade');
            $table->string('sender_type'); // 'professor' ou 'aluno'
            $table->unsignedBigInteger('sender_id'); // ID do professor (user_id) ou aluno (aluno_id)
            $table->text('message');
            $table->boolean('is_system_message')->default(false); // Mensagens do sistema (ex: "Fulano entrou")
            $table->timestamps();
            
            $table->index(['meeting_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_messages');
    }
};

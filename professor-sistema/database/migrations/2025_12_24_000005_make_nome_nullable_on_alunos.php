<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('alunos')) {
            return;
        }

        if (Schema::hasColumn('alunos', 'nome')) {
            DB::statement('ALTER TABLE `alunos` MODIFY `nome` varchar(255) NULL;');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('alunos')) {
            return;
        }

        if (Schema::hasColumn('alunos', 'nome')) {
            DB::statement('ALTER TABLE `alunos` MODIFY `nome` varchar(255) NOT NULL;');
        }
    }
};

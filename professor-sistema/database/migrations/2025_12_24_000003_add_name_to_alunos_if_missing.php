<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('alunos')) {
            return;
        }

        if (! Schema::hasColumn('alunos', 'name')) {
            Schema::table('alunos', function (Blueprint $table) {
                $table->string('name')->nullable()->after('id');
            });

            // If there is a 'nome' column, copy its data
            if (Schema::hasColumn('alunos', 'nome')) {
                DB::statement('UPDATE alunos SET name = nome WHERE name IS NULL OR name = ""');
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('alunos') && Schema::hasColumn('alunos', 'name')) {
            Schema::table('alunos', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }
    }
};

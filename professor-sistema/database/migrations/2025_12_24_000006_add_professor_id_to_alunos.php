<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('alunos')) {
            return;
        }

        if (! Schema::hasColumn('alunos', 'professor_id')) {
            Schema::table('alunos', function (Blueprint $table) {
                $table->unsignedBigInteger('professor_id')->nullable()->after('user_id');
                $table->foreign('professor_id')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('alunos')) {
            return;
        }

        if (Schema::hasColumn('alunos', 'professor_id')) {
            Schema::table('alunos', function (Blueprint $table) {
                $table->dropForeign(['professor_id']);
                $table->dropColumn('professor_id');
            });
        }
    }
};

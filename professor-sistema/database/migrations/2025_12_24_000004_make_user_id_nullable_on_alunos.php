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

        // Use raw SQL to alter column to nullable to avoid requiring doctrine/dbal
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `alunos` MODIFY `user_id` bigint(20) unsigned NULL;');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('alunos')) {
            return;
        }
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `alunos` MODIFY `user_id` bigint(20) unsigned NOT NULL;');
        }
    }
};

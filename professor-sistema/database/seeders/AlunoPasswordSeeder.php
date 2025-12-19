<?php

namespace Database\Seeders;

use App\Models\Aluno;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AlunoPasswordSeeder extends Seeder
{
    public function run(): void
    {
        $alunos = Aluno::whereNull('password')->get();

        foreach ($alunos as $aluno) {
            // Gerar senha padrão baseada no email ou usar "123456"
            if ($aluno->email) {
                $senhaTemporaria = substr(str_replace(['@', '.', '-', '_'], '', $aluno->email), 0, 6);
                $senha = $senhaTemporaria ?: '123456';
            } else {
                $senha = '123456';
            }

            $aluno->update([
                'password' => Hash::make($senha),
            ]);

            $this->command->info("Senha gerada para {$aluno->nome}: {$senha}");
        }

        $this->command->info('Senhas geradas com sucesso para ' . $alunos->count() . ' alunos!');
    }
}

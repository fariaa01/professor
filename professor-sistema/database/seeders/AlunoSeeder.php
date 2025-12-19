<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AlunoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::first();
        
        if (!$user) {
            return;
        }

        // Criar alunos de exemplo SEM tags (professor vai criar suas próprias)
        $alunos = [
            [
                'nome' => 'João Silva',
                'email' => 'joao.silva@email.com',
                'telefone' => '(11) 98765-4321',
                'valor_aula' => 80.00,
                'data_inicio' => '2025-01-15',
                'ativo' => true,
                'horarios' => [
                    ['dia_semana' => 1, 'hora_inicio' => '14:00', 'hora_fim' => '15:30'], // Segunda 14h
                    ['dia_semana' => 3, 'hora_inicio' => '14:00', 'hora_fim' => '15:30'], // Quarta 14h
                ]
            ],
            [
                'nome' => 'Maria Santos',
                'email' => 'maria.santos@email.com',
                'telefone' => '(11) 97654-3210',
                'valor_aula' => 100.00,
                'data_inicio' => '2025-02-01',
                'ativo' => true,
                'horarios' => [
                    ['dia_semana' => 2, 'hora_inicio' => '16:00', 'hora_fim' => '17:30'], // Terça 16h
                    ['dia_semana' => 4, 'hora_inicio' => '16:00', 'hora_fim' => '17:30'], // Quinta 16h
                ]
            ],
            [
                'nome' => 'Pedro Oliveira',
                'email' => 'pedro.oliveira@email.com',
                'telefone' => '(11) 96543-2109',
                'valor_aula' => 70.00,
                'data_inicio' => '2025-03-10',
                'ativo' => true,
                'horarios' => [
                    ['dia_semana' => 5, 'hora_inicio' => '10:00', 'hora_fim' => '11:30'], // Sexta 10h
                ]
            ],
            [
                'nome' => 'Ana Costa',
                'email' => 'ana.costa@email.com',
                'telefone' => '(11) 95432-1098',
                'valor_aula' => 90.00,
                'data_inicio' => '2025-01-20',
                'ativo' => true,
                'horarios' => [
                    ['dia_semana' => 1, 'hora_inicio' => '18:00', 'hora_fim' => '19:30'], // Segunda 18h
                    ['dia_semana' => 4, 'hora_inicio' => '18:00', 'hora_fim' => '19:30'], // Quinta 18h
                ]
            ],
            [
                'nome' => 'Lucas Ferreira',
                'email' => 'lucas.ferreira@email.com',
                'telefone' => '(11) 94321-0987',
                'valor_aula' => 75.00,
                'data_inicio' => '2024-11-05',
                'ativo' => false, // Inativo
                'horarios' => []
            ],
        ];

        foreach ($alunos as $alunoData) {
            $horarios = $alunoData['horarios'];
            unset($alunoData['horarios']);
            
            $aluno = \App\Models\Aluno::create([
                'user_id' => $user->id,
                ...$alunoData
            ]);

            // Criar horários
            foreach ($horarios as $horario) {
                $inicio = \Carbon\Carbon::parse($horario['hora_inicio']);
                $fim = \Carbon\Carbon::parse($horario['hora_fim']);
                $duracao = $fim->diffInMinutes($inicio);

                $aluno->horarios()->create([
                    'dia_semana' => $horario['dia_semana'],
                    'hora_inicio' => $horario['hora_inicio'],
                    'hora_fim' => $horario['hora_fim'],
                    'duracao_minutos' => $duracao,
                    'ativo' => true,
                ]);
            }
        }
    }
}

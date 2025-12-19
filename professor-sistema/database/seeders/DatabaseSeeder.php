<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar usuário professor
        $professor = User::factory()->create([
            'name' => 'Professor João Silva',
            'email' => 'professor@exemplo.com',
            'password' => bcrypt('password'),
        ]);

        // Criar alunos com dados financeiros
        $alunos = [
            ['nome' => 'Maria Santos', 'email' => 'maria@exemplo.com', 'telefone' => '(11) 98765-4321', 'valor_aula' => 80.00, 'data_inicio' => now()->subMonths(3), 'ativo' => true],
            ['nome' => 'Pedro Costa', 'email' => 'pedro@exemplo.com', 'telefone' => '(11) 98765-1234', 'valor_aula' => 100.00, 'data_inicio' => now()->subMonths(6), 'ativo' => true],
            ['nome' => 'Ana Silva', 'email' => 'ana@exemplo.com', 'telefone' => '(11) 98765-5678', 'valor_aula' => 75.00, 'endereco' => 'Rua das Flores, 123', 'responsavel' => 'José Silva', 'telefone_responsavel' => '(11) 98765-9999', 'data_inicio' => now()->subMonths(2), 'ativo' => true],
            ['nome' => 'Lucas Oliveira', 'email' => 'lucas@exemplo.com', 'telefone' => '(11) 98765-8765', 'valor_aula' => 90.00, 'data_inicio' => now()->subMonths(4), 'ativo' => true],
            ['nome' => 'Carla Souza', 'email' => 'carla@exemplo.com', 'telefone' => '(11) 98765-4567', 'valor_aula' => 85.00, 'observacoes' => 'Aluna dedicada, gosta de exercícios práticos', 'data_inicio' => now()->subMonths(1), 'ativo' => true],
        ];

        $alunosModels = [];
        foreach ($alunos as $aluno) {
            $alunosModels[] = $professor->alunos()->create($aluno);
        }

        // Criar horários recorrentes para os alunos
        // Maria - Segunda, Quarta e Sexta 10h-11h
        $alunosModels[0]->horarios()->createMany([
            ['dia_semana' => 1, 'hora_inicio' => '10:00', 'hora_fim' => '11:00', 'duracao_minutos' => 60, 'ativo' => true],
            ['dia_semana' => 3, 'hora_inicio' => '10:00', 'hora_fim' => '11:00', 'duracao_minutos' => 60, 'ativo' => true],
            ['dia_semana' => 5, 'hora_inicio' => '10:00', 'hora_fim' => '11:00', 'duracao_minutos' => 60, 'ativo' => true],
        ]);

        // Pedro - Terça e Quinta 14h-15h30
        $alunosModels[1]->horarios()->createMany([
            ['dia_semana' => 2, 'hora_inicio' => '14:00', 'hora_fim' => '15:30', 'duracao_minutos' => 90, 'ativo' => true],
            ['dia_semana' => 4, 'hora_inicio' => '14:00', 'hora_fim' => '15:30', 'duracao_minutos' => 90, 'ativo' => true],
        ]);

        // Ana - Segunda e Quarta 16h-17h
        $alunosModels[2]->horarios()->createMany([
            ['dia_semana' => 1, 'hora_inicio' => '16:00', 'hora_fim' => '17:00', 'duracao_minutos' => 60, 'ativo' => true],
            ['dia_semana' => 3, 'hora_inicio' => '16:00', 'hora_fim' => '17:00', 'duracao_minutos' => 60, 'ativo' => true],
        ]);

        // Lucas - Terça, Quarta e Quinta 18h-19h
        $alunosModels[3]->horarios()->createMany([
            ['dia_semana' => 2, 'hora_inicio' => '18:00', 'hora_fim' => '19:00', 'duracao_minutos' => 60, 'ativo' => true],
            ['dia_semana' => 3, 'hora_inicio' => '18:00', 'hora_fim' => '19:00', 'duracao_minutos' => 60, 'ativo' => true],
            ['dia_semana' => 4, 'hora_inicio' => '18:00', 'hora_fim' => '19:00', 'duracao_minutos' => 60, 'ativo' => true],
        ]);

        // Carla - Segunda e Sexta 15h-16h
        $alunosModels[4]->horarios()->createMany([
            ['dia_semana' => 1, 'hora_inicio' => '15:00', 'hora_fim' => '16:00', 'duracao_minutos' => 60, 'ativo' => true],
            ['dia_semana' => 5, 'hora_inicio' => '15:00', 'hora_fim' => '16:00', 'duracao_minutos' => 60, 'ativo' => true],
        ]);

        // Criar aulas para esta semana e semana passada
        $hoje = \Carbon\Carbon::now();
        
        // Aulas da semana passada (realizadas)
        for ($i = -7; $i < 0; $i++) {
            $data = $hoje->copy()->addDays($i);
            if ($data->dayOfWeek >= 1 && $data->dayOfWeek <= 5) { // Segunda a sexta
                foreach ([9, 14, 16] as $hora) {
                    if (rand(0, 1)) {
                        $aluno = $alunosModels[array_rand($alunosModels)];
                        $status = rand(0, 10) > 2 ? 'realizada' : 'cancelada_aluno';
                        $statusPagamento = $status === 'realizada' ? (rand(0, 10) > 2 ? 'pago' : (rand(0, 1) ? 'pendente' : 'atrasado')) : 'pendente';
                        
                        $professor->aulas()->create([
                            'aluno_id' => $aluno->id,
                            'data_hora' => $data->copy()->setTime($hora, 0),
                            'duracao_minutos' => 60,
                            'valor' => $aluno->valor_aula,
                            'forma_pagamento' => $statusPagamento === 'pago' ? ['dinheiro', 'pix', 'cartao'][rand(0, 2)] : null,
                            'status_pagamento' => $statusPagamento,
                            'status' => $status,
                            'conteudo' => $status === 'realizada' ? ['Revisão de conceitos', 'Exercícios práticos', 'Novo conteúdo'][rand(0, 2)] : null,
                            'observacoes' => rand(0, 3) == 0 ? 'Aula muito produtiva!' : null,
                        ]);
                    }
                }
            }
        }

        // Aulas desta semana (agendadas e algumas realizadas)
        for ($i = 0; $i < 7; $i++) {
            $data = $hoje->copy()->addDays($i);
            if ($data->dayOfWeek >= 1 && $data->dayOfWeek <= 5) {
                foreach ([9, 14, 16, 18] as $hora) {
                    if (rand(0, 2) > 0) {
                        $aluno = $alunosModels[array_rand($alunosModels)];
                        $dataAula = $data->copy()->setTime($hora, 0);
                        $status = $dataAula->isPast() ? 'realizada' : 'agendada';
                        $statusPagamento = $status === 'realizada' ? (rand(0, 10) > 3 ? 'pago' : 'pendente') : 'pendente';
                        
                        $professor->aulas()->create([
                            'aluno_id' => $aluno->id,
                            'data_hora' => $dataAula,
                            'duracao_minutos' => 60,
                            'valor' => $aluno->valor_aula,
                            'forma_pagamento' => $statusPagamento === 'pago' ? ['dinheiro', 'pix', 'cartao'][rand(0, 2)] : null,
                            'status_pagamento' => $statusPagamento,
                            'status' => $status,
                            'conteudo' => $status === 'realizada' ? ['Revisão geral', 'Exercícios avançados', 'Preparação para prova', 'Novo tópico'][rand(0, 3)] : null,
                            'observacoes' => null,
                        ]);
                    }
                }
            }
        }

        // Aulas da próxima semana (agendadas)
        for ($i = 7; $i < 14; $i++) {
            $data = $hoje->copy()->addDays($i);
            if ($data->dayOfWeek >= 1 && $data->dayOfWeek <= 5) {
                foreach ([10, 14, 16] as $hora) {
                    if (rand(0, 1)) {
                        $aluno = $alunosModels[array_rand($alunosModels)];
                        $professor->aulas()->create([
                            'aluno_id' => $aluno->id,
                            'data_hora' => $data->copy()->setTime($hora, 0),
                            'duracao_minutos' => 60,
                            'valor' => $aluno->valor_aula,
                            'status_pagamento' => 'pendente',
                            'status' => 'agendada',
                            'observacoes' => null,
                        ]);
                    }
                }
            }
        }
    }
}

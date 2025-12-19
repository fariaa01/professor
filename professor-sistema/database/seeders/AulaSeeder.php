<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AulaSeeder extends Seeder
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

        // Buscar alunos ativos com horários
        $alunos = \App\Models\Aluno::where('user_id', $user->id)
            ->where('ativo', true)
            ->with('horarios')
            ->get();

        // Gerar aulas para as próximas 4 semanas
        $startDate = \Carbon\Carbon::now()->startOfWeek();
        $endDate = \Carbon\Carbon::now()->addWeeks(4)->endOfWeek();

        foreach ($alunos as $aluno) {
            foreach ($aluno->horarios as $horario) {
                if (!$horario->ativo) continue;

                // Encontrar todas as datas para este dia da semana
                $currentDate = $startDate->copy();
                
                while ($currentDate->lte($endDate)) {
                    if ($currentDate->dayOfWeek === $horario->dia_semana) {
                        // Criar aula para esta data
                        $dataHora = $currentDate->copy()
                            ->setTimeFromTimeString($horario->hora_inicio);

                        // Verificar se a data é no futuro ou nas últimas 2 semanas
                        if ($dataHora->gte(\Carbon\Carbon::now()->subWeeks(2))) {
                            // Determinar status: passadas = realizada, futuras = agendada
                            $status = $dataHora->isPast() ? 'realizada' : 'agendada';
                            
                            \App\Models\Aula::create([
                                'user_id' => $user->id,
                                'aluno_id' => $aluno->id,
                                'data_hora' => $dataHora,
                                'duracao_minutos' => $horario->duracao_minutos,
                                'status' => $status,
                                'status_pagamento' => $status === 'realizada' ? (rand(0, 1) ? 'pago' : 'pendente') : 'pendente',
                                'valor' => $aluno->valor_aula,
                            ]);
                        }
                    }
                    
                    $currentDate->addDay();
                }
            }
        }

        // Adicionar algumas aulas já realizadas no passado (últimas 2 semanas)
        $pastStart = \Carbon\Carbon::now()->subWeeks(2)->startOfWeek();
        $pastEnd = \Carbon\Carbon::now()->subDay();

        foreach ($alunos->take(3) as $aluno) {
            foreach ($aluno->horarios as $horario) {
                if (!$horario->ativo) continue;

                $currentDate = $pastStart->copy();
                
                while ($currentDate->lte($pastEnd)) {
                    if ($currentDate->dayOfWeek === $horario->dia_semana) {
                        $dataHora = $currentDate->copy()
                            ->setTimeFromTimeString($horario->hora_inicio);

                        // Algumas aulas realizadas, algumas com falta
                        $rand = rand(1, 10);
                        $status = $rand <= 7 ? 'realizada' : 'cancelada_aluno';
                        
                        \App\Models\Aula::create([
                            'user_id' => $user->id,
                            'aluno_id' => $aluno->id,
                            'data_hora' => $dataHora,
                            'duracao_minutos' => $horario->duracao_minutos,
                            'status' => $status,
                            'status_pagamento' => $status === 'realizada' ? (rand(0, 1) ? 'pago' : 'pendente') : 'pendente',
                            'valor' => $aluno->valor_aula,
                        ]);
                    }
                    
                    $currentDate->addDay();
                }
            }
        }
    }
}

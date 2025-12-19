<?php

namespace Database\Seeders;

use App\Models\Aluno;
use App\Models\Plano;
use App\Models\Parcela;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PlanoSeeder extends Seeder
{
    public function run(): void
    {
        $alunos = Aluno::all();

        if ($alunos->isEmpty()) {
            $this->command->warn('Nenhum aluno encontrado. Execute AlunoSeeder primeiro.');
            return;
        }

        foreach ($alunos as $index => $aluno) {
            // Criar plano baseado no índice para variar os tipos
            $tipoPlanosExemplo = ['por_aula', 'pacote', 'mensalidade'];
            $tipoPlan = $tipoPlanosExemplo[$index % 3];

            $dataInicio = Carbon::now()->startOfMonth();
            
            // Criar plano com base no tipo
            if ($tipoPlan === 'por_aula') {
                // Pagamento por aula individual
                $plano = Plano::create([
                    'aluno_id' => $aluno->id,
                    'tipo_plano' => 'por_aula',
                    'valor_aula' => 80.00,
                    'data_inicio' => $dataInicio,
                    'ativo' => true,
                ]);

                // Criar 4 parcelas pendentes (próximas aulas a pagar)
                for ($i = 1; $i <= 4; $i++) {
                    Parcela::create([
                        'plano_id' => $plano->id,
                        'numero_parcela' => $i,
                        'total_parcelas' => 4,
                        'valor' => 80.00,
                        'data_vencimento' => Carbon::now()->addWeeks($i),
                        'status_pagamento' => 'pendente',
                    ]);
                }

            } elseif ($tipoPlan === 'pacote') {
                // Pacote de 12 aulas
                $plano = Plano::create([
                    'aluno_id' => $aluno->id,
                    'tipo_plano' => 'pacote',
                    'valor_total' => 840.00, // 12 aulas × R$ 70
                    'quantidade_aulas' => 12,
                    'data_inicio' => $dataInicio,
                    'data_fim' => $dataInicio->copy()->addMonths(3),
                    'ativo' => true,
                ]);

                // Dividir em 3 parcelas
                $valorParcela = 840.00 / 3;
                for ($i = 1; $i <= 3; $i++) {
                    Parcela::create([
                        'plano_id' => $plano->id,
                        'numero_parcela' => $i,
                        'total_parcelas' => 3,
                        'valor' => $valorParcela,
                        'data_vencimento' => $dataInicio->copy()->addMonths($i - 1),
                        'status_pagamento' => $i === 1 ? 'pago' : ($i === 2 && Carbon::now() > $dataInicio->copy()->addMonths(1) ? 'atrasado' : 'pendente'),
                        'data_pagamento' => $i === 1 ? $dataInicio : null,
                        'forma_pagamento' => $i === 1 ? 'pix' : null,
                    ]);
                }

            } else {
                // Mensalidade
                $plano = Plano::create([
                    'aluno_id' => $aluno->id,
                    'tipo_plano' => 'mensalidade',
                    'valor_total' => 400.00,
                    'data_inicio' => $dataInicio,
                    'ativo' => true,
                ]);

                // Criar 6 mensalidades (6 meses)
                for ($i = 1; $i <= 6; $i++) {
                    $vencimento = $dataInicio->copy()->addMonths($i - 1)->day(10);
                    $jaPagou = $i === 1; // Primeira parcela paga
                    $atrasada = !$jaPagou && Carbon::now()->greaterThan($vencimento);
                    
                    Parcela::create([
                        'plano_id' => $plano->id,
                        'numero_parcela' => $i,
                        'total_parcelas' => 6,
                        'valor' => 400.00,
                        'data_vencimento' => $vencimento,
                        'status_pagamento' => $jaPagou ? 'pago' : ($atrasada ? 'atrasado' : 'pendente'),
                        'data_pagamento' => $jaPagou ? $vencimento : null,
                        'forma_pagamento' => $jaPagou ? 'transferencia' : null,
                    ]);
                }
            }
        }

        $this->command->info('Planos e parcelas criados com sucesso!');
    }
}

<?php

namespace App\Http\Controllers\Aluno;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use App\Models\Plano;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard do aluno com resumo geral
     */
    public function index()
    {
        $aluno = auth('aluno')->user();

        // Próximas aulas (até 5)
        $proximasAulas = Aula::where('aluno_id', $aluno->id)
            ->where('user_id', $aluno->user_id)
            ->where('data_hora', '>=', Carbon::now())
            ->whereIn('status', ['pendente', 'reagendada'])
            ->orderBy('data_hora', 'asc')
            ->limit(5)
            ->get()
            ->map(function($aula) {
                return [
                    'id' => $aula->id,
                    'data_hora' => $aula->data_hora->format('d/m/Y H:i'),
                    'data_hora_iso' => $aula->data_hora->toIso8601String(),
                    'duracao_minutos' => $aula->duracao_minutos,
                    'status' => $aula->status,
                ];
            });

        // Aulas recentes (últimas 5 realizadas)
        $aulasRecentes = Aula::where('aluno_id', $aluno->id)
            ->where('user_id', $aluno->user_id)
            ->where('status', 'realizada')
            ->orderBy('data_hora', 'desc')
            ->limit(5)
            ->get()
            ->map(function($aula) {
                return [
                    'id' => $aula->id,
                    'data_hora' => $aula->data_hora->format('d/m/Y H:i'),
                    'duracao_minutos' => $aula->duracao_minutos,
                    'conteudo' => $aula->conteudo ? substr($aula->conteudo, 0, 100) . '...' : null,
                    'tem_materiais' => !empty($aula->materiais),
                    'tem_exercicios' => !empty($aula->exercicios),
                ];
            });

        // Estatísticas gerais
        $totalAulas = Aula::where('aluno_id', $aluno->id)
            ->where('user_id', $aluno->user_id)
            ->count();

        $aulasRealizadas = Aula::where('aluno_id', $aluno->id)
            ->where('user_id', $aluno->user_id)
            ->where('status', 'realizada')
            ->count();

        $faltasAluno = Aula::where('aluno_id', $aluno->id)
            ->where('user_id', $aluno->user_id)
            ->where('status', 'falta')
            ->count();

        $cargaHoraria = Aula::where('aluno_id', $aluno->id)
            ->where('user_id', $aluno->user_id)
            ->where('status', 'realizada')
            ->sum('duracao_minutos');

        // Plano ativo
        $planoAtivo = Plano::where('aluno_id', $aluno->id)
            ->where('ativo', true)
            ->with(['parcelas' => function($query) {
                $query->whereIn('status_pagamento', ['pendente', 'atrasado'])
                    ->orderBy('data_vencimento', 'asc')
                    ->limit(3);
            }])
            ->first();

        $planoData = null;
        if ($planoAtivo) {
            $planoData = [
                'tipo_plano' => $planoAtivo->tipo_plano,
                'tipo_plano_nome' => $planoAtivo->tipo_plano_nome,
                'valor_aula' => $planoAtivo->valor_aula,
                'valor_total' => $planoAtivo->valor_total,
                'quantidade_aulas' => $planoAtivo->quantidade_aulas,
                'data_inicio' => $planoAtivo->data_inicio->format('d/m/Y'),
                'data_fim' => $planoAtivo->data_fim?->format('d/m/Y'),
                'proximas_parcelas' => $planoAtivo->parcelas->map(function($parcela) {
                    return [
                        'numero_parcela' => $parcela->numero_parcela,
                        'total_parcelas' => $parcela->total_parcelas,
                        'valor' => $parcela->valor,
                        'data_vencimento' => $parcela->data_vencimento->format('d/m/Y'),
                        'status_pagamento' => $parcela->status_pagamento,
                    ];
                }),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'aluno' => [
                    'nome' => $aluno->nome,
                    'email' => $aluno->email,
                ],
                'estatisticas' => [
                    'total_aulas' => $totalAulas,
                    'aulas_realizadas' => $aulasRealizadas,
                    'faltas' => $faltasAluno,
                    'carga_horaria_minutos' => $cargaHoraria,
                    'carga_horaria_horas' => round($cargaHoraria / 60, 1),
                ],
                'proximas_aulas' => $proximasAulas,
                'aulas_recentes' => $aulasRecentes,
                'plano' => $planoData,
            ],
        ]);
    }
}

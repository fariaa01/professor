<?php

namespace App\Http\Controllers\Aluno;

use App\Http\Controllers\Controller;
use App\Models\Plano;
use App\Models\Parcela;

class PagamentoController extends Controller
{
    /**
     * Informações do plano do aluno
     */
    public function plano()
    {
        $aluno = auth('aluno')->user();

        $planoAtivo = Plano::where('aluno_id', $aluno->id)
            ->where('ativo', true)
            ->first();

        if (!$planoAtivo) {
            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Nenhum plano ativo encontrado.',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $planoAtivo->id,
                'tipo_plano' => $planoAtivo->tipo_plano,
                'tipo_plano_nome' => $planoAtivo->tipo_plano_nome,
                'valor_aula' => $planoAtivo->valor_aula,
                'valor_total' => $planoAtivo->valor_total,
                'quantidade_aulas' => $planoAtivo->quantidade_aulas,
                'data_inicio' => $planoAtivo->data_inicio->format('d/m/Y'),
                'data_fim' => $planoAtivo->data_fim?->format('d/m/Y'),
                'observacoes' => $planoAtivo->observacoes,
            ],
        ]);
    }

    /**
     * Listar todas as parcelas do aluno
     */
    public function parcelas()
    {
        $aluno = auth('aluno')->user();

        $planoAtivo = Plano::where('aluno_id', $aluno->id)
            ->where('ativo', true)
            ->first();

        if (!$planoAtivo) {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'Nenhum plano ativo encontrado.',
            ]);
        }

        $parcelas = Parcela::where('plano_id', $planoAtivo->id)
            ->orderBy('data_vencimento', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $parcelas->map(function($parcela) {
                return [
                    'id' => $parcela->id,
                    'numero_parcela' => $parcela->numero_parcela,
                    'total_parcelas' => $parcela->total_parcelas,
                    'parcela_formatada' => $parcela->parcela_formatada,
                    'valor' => $parcela->valor,
                    'data_vencimento' => $parcela->data_vencimento->format('d/m/Y'),
                    'data_pagamento' => $parcela->data_pagamento?->format('d/m/Y'),
                    'status_pagamento' => $parcela->status_pagamento,
                    'forma_pagamento' => $parcela->forma_pagamento,
                ];
            }),
        ]);
    }

    /**
     * Resumo financeiro do aluno
     */
    public function resumo()
    {
        $aluno = auth('aluno')->user();

        $planoAtivo = Plano::where('aluno_id', $aluno->id)
            ->where('ativo', true)
            ->first();

        if (!$planoAtivo) {
            return response()->json([
                'success' => true,
                'data' => [
                    'total_plano' => 0,
                    'total_pago' => 0,
                    'total_pendente' => 0,
                    'total_atrasado' => 0,
                    'parcelas_pagas' => 0,
                    'parcelas_pendentes' => 0,
                ],
            ]);
        }

        $parcelas = Parcela::where('plano_id', $planoAtivo->id)->get();

        $totalPago = $parcelas->where('status_pagamento', 'pago')->sum('valor');
        $totalPendente = $parcelas->where('status_pagamento', 'pendente')->sum('valor');
        $totalAtrasado = $parcelas->where('status_pagamento', 'atrasado')->sum('valor');
        
        $parcelasPagas = $parcelas->where('status_pagamento', 'pago')->count();
        $parcelasPendentes = $parcelas->whereIn('status_pagamento', ['pendente', 'atrasado'])->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_plano' => $planoAtivo->valor_total ?? ($planoAtivo->valor_aula * $planoAtivo->quantidade_aulas),
                'total_pago' => $totalPago,
                'total_pendente' => $totalPendente,
                'total_atrasado' => $totalAtrasado,
                'parcelas_pagas' => $parcelasPagas,
                'parcelas_pendentes' => $parcelasPendentes,
            ],
        ]);
    }
}

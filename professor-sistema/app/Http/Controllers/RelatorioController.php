<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use App\Models\Aula;
use App\Models\Plano;
use App\Models\Parcela;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RelatorioController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Definir período padrão (mês atual)
        $dataInicio = $request->filled('data_inicio') 
            ? Carbon::parse($request->data_inicio)->startOfDay()
            : Carbon::now()->startOfMonth();
            
        $dataFim = $request->filled('data_fim')
            ? Carbon::parse($request->data_fim)->endOfDay()
            : Carbon::now()->endOfMonth();

        // Filtro por aluno (opcional)
        $alunoId = $request->input('aluno_id');
        
        // Query base de aulas
        $query = Aula::where('user_id', $user->id)
            ->whereBetween('data_hora', [$dataInicio, $dataFim])
            ->with('aluno');

        if ($alunoId) {
            $query->where('aluno_id', $alunoId);
        }

        $aulas = $query->get();

        // Estatísticas Gerais
        $totalAulas = $aulas->count();
        $aulasRealizadas = $aulas->where('status', 'realizada')->count();
        $faltasAluno = $aulas->where('status', 'cancelada_aluno')->count();
        $aulasAgendadas = $aulas->where('status', 'agendada')->count();
        $cargaHoraria = $aulas->where('status', 'realizada')->sum('duracao_minutos');
        
        // Estatísticas Financeiras (apenas aulas realizadas)
        $aulasRealizadasComValor = $aulas->where('status', 'realizada');
        $faturamentoTotal = $aulasRealizadasComValor->sum('valor');
        $valorRecebido = $aulasRealizadasComValor->where('status_pagamento', 'pago')->sum('valor');
        $valorPendente = $aulasRealizadasComValor->where('status_pagamento', 'pendente')->sum('valor');
        $valorAtrasado = $aulasRealizadasComValor->where('status_pagamento', 'atrasado')->sum('valor');
        
        // Aulas por aluno (ranking) - com planos e parcelas
        $aulasPorAluno = $aulas->groupBy('aluno_id')->map(function($aulasByAluno) {
            $aluno = $aulasByAluno->first()->aluno;
            $aulasRealizadasAluno = $aulasByAluno->where('status', 'realizada');
            
            // Buscar plano ativo do aluno
            $planoAtivo = Plano::where('aluno_id', $aluno->id)
                ->where('ativo', true)
                ->with(['parcelas' => function($query) {
                    $query->whereIn('status_pagamento', ['pendente', 'atrasado'])
                        ->orderBy('data_vencimento', 'asc');
                }])
                ->first();

            return [
                'aluno' => $aluno,
                'total' => $aulasByAluno->count(),
                'realizadas' => $aulasRealizadasAluno->count(),
                'faltas' => $aulasByAluno->where('status', 'cancelada_aluno')->count(),
                'carga_horaria' => $aulasRealizadasAluno->sum('duracao_minutos'),
                'faturamento' => $aulasRealizadasAluno->sum('valor'),
                'recebido' => $aulasRealizadasAluno->where('status_pagamento', 'pago')->sum('valor'),
                'pendente' => $aulasRealizadasAluno->where('status_pagamento', 'pendente')->sum('valor'),
                'plano' => $planoAtivo,
                'proximas_parcelas' => $planoAtivo ? $planoAtivo->parcelas->take(5) : collect([]),
            ];
        })->sortByDesc('total')->values();

        // Histórico de aulas (paginado)
        $historicoAulas = $query->orderBy('data_hora', 'desc')->paginate(20);

        // Lista de alunos para o filtro
        $alunos = Aluno::where('user_id', $user->id)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        // Dados para gráficos
        $aulasPorDia = $aulas->groupBy(function($aula) {
            return $aula->data_hora->format('Y-m-d');
        })->map->count();

        return view('relatorios.index', compact(
            'dataInicio',
            'dataFim',
            'alunoId',
            'totalAulas',
            'aulasRealizadas',
            'faltasAluno',
            'aulasAgendadas',
            'cargaHoraria',
            'faturamentoTotal',
            'valorRecebido',
            'valorPendente',
            'valorAtrasado',
            'aulasPorAluno',
            'historicoAulas',
            'alunos'
        ));
    }
}

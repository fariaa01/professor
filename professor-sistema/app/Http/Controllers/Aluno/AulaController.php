<?php

namespace App\Http\Controllers\Aluno;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AulaController extends Controller
{
    /**
     * Listar todas as aulas do aluno
     */
    public function index(Request $request)
    {
        $aluno = auth('aluno')->user();

        $query = Aula::where('aluno_id', $aluno->id)
            ->where('user_id', $aluno->user_id)
            ->with('tags');

        // Filtro por período
        if ($request->has('periodo')) {
            $now = Carbon::now();
            if ($request->periodo === 'futuras') {
                $query->where('data_hora', '>=', $now);
            } elseif ($request->periodo === 'passadas') {
                $query->where('data_hora', '<', $now);
            }
        }

        // Filtro por status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por tag
        if ($request->has('tag_id')) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->where('tags.id', $request->tag_id);
            });
        }

        // Ordenação
        $orderBy = $request->get('ordem', 'desc');
        $query->orderBy('data_hora', $orderBy);

        $aulas = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $aulas->map(function($aula) {
                $dataHora = $aula->data_hora;
                $dataFormatada = $dataHora->locale('pt_BR')->isoFormat('dddd, DD [de] MMMM [de] YYYY [às] HH:mm');
                
                return [
                    'id' => $aula->id,
                    'data_hora' => ucfirst($dataFormatada),
                    'data_hora_iso' => $aula->data_hora->toIso8601String(),
                    'duracao_minutos' => $aula->duracao_minutos,
                    'status' => $aula->status,
                    'status_nome' => $this->getStatusNome($aula->status),
                    'conteudo' => $aula->conteudo,
                    'conteudo_resumo' => $aula->conteudo ? (strlen($aula->conteudo) > 150 ? substr($aula->conteudo, 0, 150) . '...' : $aula->conteudo) : null,
                    'tem_materiais' => !empty($aula->materiais),
                    'tem_exercicios' => !empty($aula->exercicios),
                    'tem_dificuldades' => !empty($aula->dificuldades),
                    'tags' => $aula->tags->map(function($tag) {
                        return [
                            'id' => $tag->id,
                            'nome' => $tag->nome,
                            'cor' => $tag->cor,
                        ];
                    }),
                    'e_futura' => $aula->data_hora->isFuture(),
                ];
            }),
            'pagination' => [
                'current_page' => $aulas->currentPage(),
                'last_page' => $aulas->lastPage(),
                'per_page' => $aulas->perPage(),
                'total' => $aulas->total(),
            ],
        ]);
    }

    /**
     * Detalhes completos de uma aula
     */
    public function show($id)
    {
        $aluno = auth('aluno')->user();

        $aula = Aula::where('id', $id)
            ->where('aluno_id', $aluno->id)
            ->where('user_id', $aluno->user_id)
            ->with('tags')
            ->first();

        if (!$aula) {
            return response()->json([
                'success' => false,
                'message' => 'Aula não encontrada.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $aula->id,
                'data_hora' => ucfirst($aula->data_hora->locale('pt_BR')->isoFormat('dddd, DD [de] MMMM [de] YYYY [às] HH:mm')),
                'data_hora_completa' => ucfirst($aula->data_hora->locale('pt_BR')->isoFormat('dddd, DD [de] MMMM [de] YYYY [às] HH:mm')),
                'data_hora_iso' => $aula->data_hora->toIso8601String(),
                'duracao_minutos' => $aula->duracao_minutos,
                'duracao_formatada' => $this->formatarDuracao($aula->duracao_minutos),
                'status' => $aula->status,
                'status_nome' => $this->getStatusNome($aula->status),
                'conteudo' => $aula->conteudo,
                'materiais' => $aula->materiais,
                'exercicios' => $aula->exercicios,
                'dificuldades' => $aula->dificuldades,
                'pontos_atencao' => $aula->pontos_atencao,
                'observacoes' => $aula->observacoes,
                'tags' => $aula->tags->map(function($tag) {
                    return [
                        'id' => $tag->id,
                        'nome' => $tag->nome,
                        'cor' => $tag->cor,
                    ];
                }),
                'e_futura' => $aula->data_hora->isFuture(),
                'e_hoje' => $aula->data_hora->isToday(),
            ],
        ]);
    }

    /**
     * Traduzir status para nome legível
     */
    private function getStatusNome($status)
    {
        $statusNomes = [
            'realizada' => 'Realizada',
            'pendente' => 'Agendada',
            'reagendada' => 'Reagendada',
            'cancelada' => 'Cancelada',
            'falta' => 'Falta',
        ];

        return $statusNomes[$status] ?? $status;
    }

    /**
     * Formatar duração em horas e minutos
     */
    private function formatarDuracao($minutos)
    {
        if ($minutos < 60) {
            return $minutos . ' min';
        }

        $horas = floor($minutos / 60);
        $mins = $minutos % 60;

        if ($mins === 0) {
            return $horas . 'h';
        }

        return $horas . 'h' . $mins . 'min';
    }
}

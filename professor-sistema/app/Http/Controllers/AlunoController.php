<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Aluno;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AlunoController extends Controller
{
    public function index(Request $request)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $query = Aluno::where('user_id', auth()->id())
            ->with(['tags'])
            ->withCount([
                'aulas', 
                'aulas as aulas_realizadas_count' => function($q) {
                    $q->where('status', 'realizada');
                },
                'aulas as faltas_mes_count' => function($q) use ($startOfMonth, $endOfMonth) {
                    $q->where('status', 'cancelada_aluno')
                      ->whereBetween('data_hora', [$startOfMonth, $endOfMonth]);
                }
            ]);

        // Filtros
        if ($request->filled('busca')) {
            $query->where(function($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->busca . '%')
                  ->orWhere('email', 'like', '%' . $request->busca . '%')
                  ->orWhere('telefone', 'like', '%' . $request->busca . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('ativo', $request->status === 'ativo');
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->where('tags.id', $request->tag);
            });
        }

        $alunos = $query->latest()->paginate(10);
        
        // Buscar próxima aula para cada aluno
        foreach ($alunos as $aluno) {
            $aluno->proxima_aula = \App\Models\Aula::where('aluno_id', $aluno->id)
                ->where('data_hora', '>=', Carbon::now())
                ->whereIn('status', ['agendada'])
                ->orderBy('data_hora')
                ->first();
        }
        
        $tags = \App\Models\Tag::where('user_id', auth()->id())
            ->withCount('alunos')
            ->orderBy('nome')
            ->get();

        return view('alunos.index', compact('alunos', 'tags'));
    }

    public function create()
    {
        $tags = \App\Models\Tag::where('user_id', auth()->id())
            ->orderBy('nome')
            ->get();
            
        return view('alunos.create', compact('tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'responsavel' => 'nullable|string|max:255',
            'telefone_responsavel' => 'nullable|string|max:20',
            'valor_aula' => 'nullable|numeric|min:0',
            'data_inicio' => 'nullable|date',
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'horarios' => 'nullable|array',
            'horarios.*.dia_semana' => 'required|integer|min:0|max:6',
            'horarios.*.hora_inicio' => 'required|date_format:H:i',
            'horarios.*.hora_fim' => 'required|date_format:H:i|after:horarios.*.hora_inicio',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['ativo'] = $request->has('ativo');

        // Gerar senha padrão para o aluno (primeiros 6 dígitos do email ou "123456")
        if (!empty($validated['email'])) {
            $senhaTemporaria = substr(str_replace(['@', '.', '-', '_'], '', $validated['email']), 0, 6);
            $validated['password'] = \Hash::make($senhaTemporaria ?: '123456');
        } else {
            $validated['password'] = \Hash::make('123456');
        }

        $aluno = Aluno::create($validated);

        // Associar tags
        if ($request->has('tags')) {
            $aluno->tags()->attach($request->tags);
        }

        // Salvar horários
        if ($request->has('horarios')) {
            foreach ($request->horarios as $horario) {
                $inicio = Carbon::parse($horario['hora_inicio']);
                $fim = Carbon::parse($horario['hora_fim']);
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

        return redirect()->route('alunos.index')
            ->with('success', 'Aluno cadastrado com sucesso!');
    }

    public function show(Aluno $aluno)
    {
        $this->authorize('view', $aluno);

        $aluno->load('horariosAtivos');

        $mesAtual = Carbon::now();
        
        // Estatísticas gerais
        $totalAulas = $aluno->aulas()->count();
        $aulasRealizadas = $aluno->aulas()->where('status', 'realizada')->count();
        $faltasAluno = $aluno->aulas()->where('status', 'cancelada_aluno')->count();
        $cargaHoraria = $aluno->aulas()->where('status', 'realizada')->sum('duracao_minutos');
        
        // Estatísticas financeiras
        $totalRecebido = $aluno->aulas()
            ->where('status_pagamento', 'pago')
            ->sum('valor');
            
        $valorPendente = $aluno->aulas()
            ->where('status_pagamento', 'pendente')
            ->sum('valor');
            
        $valorAtrasado = $aluno->aulas()
            ->where('status_pagamento', 'atrasado')
            ->sum('valor');
        
        // Estatísticas do mês
        $aulasMesAtual = $aluno->aulas()
            ->whereMonth('data_hora', $mesAtual->month)
            ->whereYear('data_hora', $mesAtual->year)
            ->count();
            
        $recebidoMesAtual = $aluno->aulas()
            ->where('status_pagamento', 'pago')
            ->whereMonth('data_hora', $mesAtual->month)
            ->whereYear('data_hora', $mesAtual->year)
            ->sum('valor');
        
        // Histórico de aulas (últimas 20)
        $historicoAulas = $aluno->aulas()
            ->orderBy('data_hora', 'desc')
            ->paginate(20);
        
        // Próximas aulas
        $proximasAulas = $aluno->aulas()
            ->where('status', 'agendada')
            ->where('data_hora', '>=', now())
            ->orderBy('data_hora')
            ->get();
        
        // Taxa de frequência
        $taxaFrequencia = $totalAulas > 0 
            ? round(($aulasRealizadas / $totalAulas) * 100, 1) 
            : 0;

        return view('alunos.show', compact(
            'aluno',
            'totalAulas',
            'aulasRealizadas',
            'faltasAluno',
            'cargaHoraria',
            'totalRecebido',
            'valorPendente',
            'valorAtrasado',
            'aulasMesAtual',
            'recebidoMesAtual',
            'historicoAulas',
            'proximasAulas',
            'taxaFrequencia'
        ));
    }

    public function edit(Aluno $aluno)
    {
        $this->authorize('update', $aluno);
        $aluno->load('horarios', 'tags');
        
        $tags = \App\Models\Tag::where('user_id', auth()->id())
            ->orderBy('nome')
            ->get();
            
        return view('alunos.edit', compact('aluno', 'tags'));
    }

    public function update(Request $request, Aluno $aluno)
    {
        $this->authorize('update', $aluno);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'responsavel' => 'nullable|string|max:255',
            'telefone_responsavel' => 'nullable|string|max:20',
            'valor_aula' => 'nullable|numeric|min:0',
            'data_inicio' => 'nullable|date',
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'horarios' => 'nullable|array',
            'horarios.*.id' => 'nullable|integer|exists:horarios_aulas,id',
            'horarios.*.dia_semana' => 'required|integer|min:0|max:6',
            'horarios.*.hora_inicio' => 'required|date_format:H:i',
            'horarios.*.hora_fim' => 'required|date_format:H:i|after:horarios.*.hora_inicio',
            'horarios.*.ativo' => 'nullable|boolean',
        ]);

        $validated['ativo'] = $request->has('ativo');

        $aluno->update($validated);

        // Sincronizar tags
        if ($request->has('tags')) {
            $aluno->tags()->sync($request->tags);
        } else {
            $aluno->tags()->detach();
        }

        // Atualizar horários
        if ($request->has('horarios')) {
            $horariosIds = [];
            
            foreach ($request->horarios as $horarioData) {
                $inicio = Carbon::parse($horarioData['hora_inicio']);
                $fim = Carbon::parse($horarioData['hora_fim']);
                $duracao = $fim->diffInMinutes($inicio);

                if (isset($horarioData['id'])) {
                    // Atualizar existente
                    $horario = $aluno->horarios()->find($horarioData['id']);
                    if ($horario) {
                        $horario->update([
                            'dia_semana' => $horarioData['dia_semana'],
                            'hora_inicio' => $horarioData['hora_inicio'],
                            'hora_fim' => $horarioData['hora_fim'],
                            'duracao_minutos' => $duracao,
                            'ativo' => $horarioData['ativo'] ?? true,
                        ]);
                        $horariosIds[] = $horario->id;
                    }
                } else {
                    // Criar novo
                    $horario = $aluno->horarios()->create([
                        'dia_semana' => $horarioData['dia_semana'],
                        'hora_inicio' => $horarioData['hora_inicio'],
                        'hora_fim' => $horarioData['hora_fim'],
                        'duracao_minutos' => $duracao,
                        'ativo' => true,
                    ]);
                    $horariosIds[] = $horario->id;
                }
            }

            // Desativar horários que não estão mais na lista
            $aluno->horarios()->whereNotIn('id', $horariosIds)->update(['ativo' => false]);
        }

        return redirect()->route('alunos.show', $aluno)
            ->with('success', 'Aluno atualizado com sucesso!');
    }

    public function destroy(Aluno $aluno)
    {
        $this->authorize('delete', $aluno);
        
        $aluno->delete();

        return redirect()->route('alunos.index')
            ->with('success', 'Aluno excluído com sucesso!');
    }

    public function toggleStatus(Aluno $aluno)
    {
        $this->authorize('update', $aluno);
        
        $aluno->update(['ativo' => !$aluno->ativo]);

        return redirect()->route('alunos.index')
            ->with('success', 'Status do aluno atualizado com sucesso!');
    }
}

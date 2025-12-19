<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use Illuminate\Http\Request;

class AulaController extends Controller
{
    public function updateStatus(Request $request, Aula $aula)
    {
        // Verificar se a aula pertence ao usuário autenticado
        if ($aula->user_id !== auth()->id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:agendada,realizada,cancelada_aluno',
        ]);

        $aula->update([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status atualizado com sucesso!',
            'aula' => [
                'id' => $aula->id,
                'status' => $aula->status,
                'status_label' => $this->getStatusLabel($aula->status),
                'status_variant' => $this->getStatusVariant($aula->status),
            ]
        ]);
    }

    public function reschedule(Request $request, Aula $aula)
    {
        // Verificar se a aula pertence ao usuário autenticado
        if ($aula->user_id !== auth()->id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $validated = $request->validate([
            'data_hora' => 'required|date|after_or_equal:today',
        ]);

        $novaDataHora = \Carbon\Carbon::parse($validated['data_hora']);
        
        $aula->update([
            'data_hora' => $novaDataHora,
            'status' => 'agendada', // Resetar status para agendada ao remarcar
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Aula reagendada com sucesso!',
            'aula' => [
                'id' => $aula->id,
                'data_hora' => $aula->data_hora->format('d/m/Y H:i'),
                'data_hora_iso' => $aula->data_hora->toIso8601String(),
                'dia_semana' => $aula->data_hora->locale('pt_BR')->isoFormat('dddd'),
                'status' => $aula->status,
                'status_label' => $this->getStatusLabel($aula->status),
                'status_variant' => $this->getStatusVariant($aula->status),
            ]
        ]);
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'agendada' => 'Agendada',
            'realizada' => 'Realizada',
            'cancelada_aluno' => 'Falta Aluno',
        ];

        return $labels[$status] ?? $status;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Filtros
        $filtroStatus = $request->input('status');
        $filtroAluno = $request->input('aluno_id');
        $filtroData = $request->input('data');
        
        // Query base
        $query = Aula::where('user_id', $user->id)
            ->with('aluno');
        
        // Aplicar filtros
        if ($filtroStatus) {
            $query->where('status', $filtroStatus);
        }
        
        if ($filtroAluno) {
            $query->where('aluno_id', $filtroAluno);
        }
        
        if ($filtroData) {
            $data = \Carbon\Carbon::parse($filtroData);
            $query->whereDate('data_hora', $data);
        }
        
        // Ordenar por data (mais recentes primeiro)
        $aulas = $query->orderBy('data_hora', 'desc')->paginate(20);
        
        // Lista de alunos para filtro
        $alunos = \App\Models\Aluno::where('user_id', $user->id)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();
        
        // Estatísticas rápidas
        $totalAulas = Aula::where('user_id', $user->id)->count();
        $aulasRealizadas = Aula::where('user_id', $user->id)->where('status', 'realizada')->count();
        $proximasAulas = Aula::where('user_id', $user->id)
            ->where('status', 'agendada')
            ->where('data_hora', '>=', now())
            ->count();
        
        return view('aulas.index', compact('aulas', 'alunos', 'totalAulas', 'aulasRealizadas', 'proximasAulas'));
    }
    
    public function show(Aula $aula)
    {
        // Verificar autorização
        if ($aula->user_id !== auth()->id()) {
            abort(403);
        }
        
        $aula->load('aluno');
        
        return view('aulas.show', compact('aula'));
    }
    
    public function edit(Aula $aula)
    {
        // Verificar autorização
        if ($aula->user_id !== auth()->id()) {
            abort(403);
        }
        
        $aula->load('aluno');
        
        return view('aulas.edit', compact('aula'));
    }
    
    public function update(Request $request, Aula $aula)
    {
        // Verificar autorização
        if ($aula->user_id !== auth()->id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'conteudo' => 'nullable|string|max:2000',
            'materiais' => 'nullable|string|max:1000',
            'exercicios' => 'nullable|string|max:1000',
            'dificuldades' => 'nullable|string|max:1000',
            'observacoes' => 'nullable|string|max:1000',
            'pontos_atencao' => 'nullable|string|max:1000',
        ]);
        
        $aula->update($validated);
        
        return redirect()->route('aulas.show', $aula)
            ->with('success', 'Registro pedagógico atualizado com sucesso!');
    }

    private function getStatusVariant($status)
    {
        $variants = [
            'agendada' => 'info',
            'realizada' => 'success',
            'cancelada_aluno' => 'warning',
        ];

        return $variants[$status] ?? 'secondary';
    }
}

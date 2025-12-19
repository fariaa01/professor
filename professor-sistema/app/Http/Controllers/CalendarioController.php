<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use App\Models\Aluno;
use App\Models\Reuniao;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarioController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Definir visualização (dia, semana, mês)
        $view = $request->input('view', 'week');
        $date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::now();
        
        // Calcular período baseado na visualização
        switch ($view) {
            case 'day':
                $startDate = $date->copy()->startOfDay();
                $endDate = $date->copy()->endOfDay();
                break;
            case 'month':
                $startDate = $date->copy()->startOfMonth()->startOfWeek();
                $endDate = $date->copy()->endOfMonth()->endOfWeek();
                break;
            default: // week
                $startDate = $date->copy()->startOfWeek();
                $endDate = $date->copy()->endOfWeek();
                break;
        }
        
        // Buscar aulas do período
        $aulasQuery = Aula::where('user_id', $user->id)
            ->whereBetween('data_hora', [$startDate, $endDate])
            ->with('aluno');
        
        // Filtro por aluno (vindo do card de alunos)
        if ($request->filled('aluno')) {
            $aulasQuery->where('aluno_id', $request->aluno);
        }
        
        $aulas = $aulasQuery->orderBy('data_hora')->get();
        
        // Buscar reuniões do período
        $reunioesQuery = Reuniao::where('user_id', $user->id)
            ->whereBetween('data_hora', [$startDate, $endDate])
            ->with('aluno');
        
        // Filtro por aluno nas reuniões também
        if ($request->filled('aluno')) {
            $reunioesQuery->where('aluno_id', $request->aluno);
        }
        
        $reunioes = $reunioesQuery->orderBy('data_hora')->get();
        
        // Lista de alunos para criar novas aulas/reuniões
        $alunos = Aluno::where('user_id', $user->id)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();
        
        // Aluno selecionado (para passar para a view)
        $alunoSelecionado = null;
        if ($request->filled('aluno')) {
            $alunoSelecionado = Aluno::where('user_id', $user->id)
                ->where('id', $request->aluno)
                ->first();
        }
        
        return view('calendario.index', compact('aulas', 'reunioes', 'alunos', 'view', 'date', 'startDate', 'endDate', 'alunoSelecionado'));
    }
    
    public function storeReuniao(Request $request)
    {
        $validated = $request->validate([
            'aluno_id' => 'nullable|exists:alunos,id',
            'data_hora' => 'required|date|after_or_equal:today',
            'duracao_minutos' => 'required|integer|min:15|max:480',
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:1000',
        ]);
        
        $validated['user_id'] = auth()->id();
        $validated['status'] = 'agendada';
        
        Reuniao::create($validated);
        
        return redirect()->route('calendario.index')
            ->with('success', 'Reunião agendada com sucesso!');
    }
    
    public function updateReuniaoStatus(Request $request, Reuniao $reuniao)
    {
        if ($reuniao->user_id !== auth()->id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }
        
        $validated = $request->validate([
            'status' => 'required|in:agendada,realizada,cancelada',
        ]);
        
        $reuniao->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Status da reunião atualizado!',
            'reuniao' => $reuniao
        ]);
    }
    
    public function rescheduleReuniao(Request $request, Reuniao $reuniao)
    {
        if ($reuniao->user_id !== auth()->id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }
        
        $validated = $request->validate([
            'data_hora' => 'required|date|after_or_equal:today',
        ]);
        
        $reuniao->update([
            'data_hora' => Carbon::parse($validated['data_hora']),
            'status' => 'agendada',
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Reunião reagendada com sucesso!',
        ]);
    }
    
    public function destroyReuniao(Reuniao $reuniao)
    {
        if ($reuniao->user_id !== auth()->id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }
        
        $reuniao->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Reunião excluída com sucesso!',
        ]);
    }
}

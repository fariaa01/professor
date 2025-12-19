<?php

namespace App\Http\Controllers;

use App\Models\Conteudo;
use App\Models\ConteudoProgresso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlunoConteudoController extends Controller
{
    /**
     * Lista conteúdos disponíveis para o aluno
     */
    public function index()
    {
        // Busca o aluno pelo email do usuário logado
        $aluno = \App\Models\Aluno::where('email', Auth::user()->email)->first();
        
        if (!$aluno) {
            return view('aluno.conteudos.index', ['conteudos' => collect()]);
        }

        $conteudos = Conteudo::disponivelParaAluno($aluno->id)
            ->with(['professor', 'progressos' => function($query) use ($aluno) {
                $query->where('aluno_id', $aluno->id);
            }])
            ->latest()
            ->get();

        return view('aluno.conteudos.index', compact('conteudos', 'aluno'));
    }

    /**
     * Exibe o conteúdo para o aluno assistir
     */
    public function show(Conteudo $conteudo)
    {
        $aluno = \App\Models\Aluno::where('email', Auth::user()->email)->first();
        
        if (!$aluno || !$conteudo->alunoTemAcesso($aluno->id)) {
            abort(403, 'Você não tem acesso a este conteúdo.');
        }

        // Busca ou cria o progresso
        $progresso = ConteudoProgresso::firstOrCreate(
            [
                'conteudo_id' => $conteudo->id,
                'aluno_id' => $aluno->id,
            ],
            [
                'progresso_segundos' => 0,
                'completo' => false,
                'visualizacoes' => 0,
            ]
        );

        // Registra visualização
        $progresso->registrarVisualizacao();

        return view('aluno.conteudos.show', compact('conteudo', 'progresso', 'aluno'));
    }

    /**
     * Atualiza o progresso do aluno (via AJAX)
     */
    public function atualizarProgresso(Request $request, Conteudo $conteudo)
    {
        $aluno = \App\Models\Aluno::where('email', Auth::user()->email)->first();
        
        if (!$aluno || !$conteudo->alunoTemAcesso($aluno->id)) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $validated = $request->validate([
            'progresso_segundos' => 'required|integer|min:0',
        ]);

        $progresso = ConteudoProgresso::where('conteudo_id', $conteudo->id)
            ->where('aluno_id', $aluno->id)
            ->first();

        if ($progresso) {
            $progresso->atualizarProgresso($validated['progresso_segundos']);
        }

        return response()->json([
            'success' => true,
            'percentual' => $progresso->percentual ?? 0,
            'completo' => $progresso->completo ?? false,
        ]);
    }
}

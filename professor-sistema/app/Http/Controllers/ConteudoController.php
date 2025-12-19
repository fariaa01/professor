<?php

namespace App\Http\Controllers;

use App\Models\Conteudo;
use App\Models\Aluno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ConteudoController extends Controller
{
    /**
     * Lista todos os conteúdos do professor
     */
    public function index()
    {
        $conteudos = Conteudo::doProfessor(Auth::id())
            ->with('progressos.aluno')
            ->latest()
            ->paginate(15);

        return view('conteudos.index', compact('conteudos'));
    }

    /**
     * Formulário de criação
     */
    public function create()
    {
        $alunos = Aluno::where('user_id', Auth::id())
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        return view('conteudos.create', compact('alunos'));
    }

    /**
     * Salva novo conteúdo
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'tipo' => 'required|in:video,pdf,texto,link',
            'url' => 'required_if:tipo,video,link|nullable|url',
            'duracao_segundos' => 'nullable|integer|min:0',
            'observacoes' => 'nullable|string',
            'alunos_ids' => 'required|array|min:1',
            'alunos_ids.*' => 'exists:alunos,id',
            'status' => 'required|in:rascunho,publicado,arquivado',
        ]);

        $validated['user_id'] = Auth::id();

        $conteudo = Conteudo::create($validated);

        return redirect()
            ->route('conteudos.show', $conteudo)
            ->with('success', 'Conteúdo criado com sucesso!');
    }

    /**
     * Exibe detalhes do conteúdo
     */
    public function show(Conteudo $conteudo)
    {
        // Verifica se o conteúdo pertence ao professor logado
        if ($conteudo->user_id !== Auth::id()) {
            abort(403);
        }

        $conteudo->load(['progressos.aluno']);

        // Estatísticas
        $totalAlunos = count($conteudo->alunos_ids ?? []);
        $alunosIniciaram = $conteudo->progressos()->whereNotNull('iniciado_em')->count();
        $alunosConcluiram = $conteudo->progressos()->where('completo', true)->count();

        return view('conteudos.show', compact('conteudo', 'totalAlunos', 'alunosIniciaram', 'alunosConcluiram'));
    }

    /**
     * Formulário de edição
     */
    public function edit(Conteudo $conteudo)
    {
        if ($conteudo->user_id !== Auth::id()) {
            abort(403);
        }

        $alunos = Aluno::where('user_id', Auth::id())
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        return view('conteudos.edit', compact('conteudo', 'alunos'));
    }

    /**
     * Atualiza conteúdo
     */
    public function update(Request $request, Conteudo $conteudo)
    {
        if ($conteudo->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'tipo' => 'required|in:video,pdf,texto,link',
            'url' => 'required_if:tipo,video,link|nullable|url',
            'duracao_segundos' => 'nullable|integer|min:0',
            'observacoes' => 'nullable|string',
            'alunos_ids' => 'required|array|min:1',
            'alunos_ids.*' => 'exists:alunos,id',
            'status' => 'required|in:rascunho,publicado,arquivado',
        ]);

        $conteudo->update($validated);

        return redirect()
            ->route('conteudos.show', $conteudo)
            ->with('success', 'Conteúdo atualizado com sucesso!');
    }

    /**
     * Remove conteúdo
     */
    public function destroy(Conteudo $conteudo)
    {
        if ($conteudo->user_id !== Auth::id()) {
            abort(403);
        }

        $conteudo->delete();

        return redirect()
            ->route('conteudos.index')
            ->with('success', 'Conteúdo removido com sucesso!');
    }
}

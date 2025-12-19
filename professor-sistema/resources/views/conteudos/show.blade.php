@extends('layouts.app')

@section('content')
<div class="py-8">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $conteudo->titulo }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('conteudos.edit', $conteudo) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    Editar
                </a>
                <form action="{{ route('conteudos.destroy', $conteudo) }}" method="POST" 
                      onsubmit="return confirm('Tem certeza que deseja excluir este conteúdo?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">
                        Excluir
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Coluna Principal -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Informações do Conteúdo -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border-t-4 border-blue-500">
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold 
                                {{ $conteudo->tipo === 'video' ? 'bg-blue-100 text-blue-800' : 
                                   ($conteudo->tipo === 'pdf' ? 'bg-red-100 text-red-800' : 
                                   ($conteudo->tipo === 'link' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ strtoupper($conteudo->tipo) }}
                            </span>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold
                                {{ $conteudo->status === 'publicado' ? 'bg-green-100 text-green-800' : 
                                   ($conteudo->status === 'rascunho' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($conteudo->status) }}
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 mb-3">Descrição</h3>
                        <p class="text-gray-700 mb-4">{{ $conteudo->descricao ?? 'Sem descrição' }}</p>

                        @if($conteudo->url)
                            <div class="mb-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Link do Conteúdo</h4>
                                <a href="{{ $conteudo->url }}" target="_blank" 
                                   class="text-blue-600 hover:underline break-all">
                                    {{ $conteudo->url }}
                                </a>
                            </div>
                        @endif

                        @if($conteudo->observacoes)
                            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
                                <h4 class="font-semibold text-blue-900 mb-2">📝 Observações do Professor</h4>
                                <p class="text-blue-800">{{ $conteudo->observacoes }}</p>
                            </div>
                        @endif

                        @if($conteudo->duracao_segundos)
                            <div class="text-sm text-gray-600">
                                ⏱️ Duração: {{ $conteudo->duracao_formatada }} minutos
                            </div>
                        @endif

                        <div class="mt-4 text-xs text-gray-500">
                            Criado em {{ $conteudo->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>

                    <!-- Progresso dos Alunos -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Progresso dos Alunos</h3>
                        
                        @if($conteudo->progressos->isEmpty())
                            <p class="text-gray-500 text-center py-8">Nenhum aluno iniciou este conteúdo ainda</p>
                        @else
                            <div class="space-y-4">
                                @foreach($conteudo->progressos as $progresso)
                                    <div class="border-l-4 {{ $progresso->completo ? 'border-green-500' : 'border-yellow-500' }} pl-4 py-2">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $progresso->aluno->nome }}</p>
                                                <p class="text-sm text-gray-600">
                                                    @if($progresso->completo)
                                                        ✅ Concluído em {{ $progresso->concluido_em->format('d/m/Y H:i') }}
                                                    @elseif($progresso->iniciado_em)
                                                        🕐 Iniciado em {{ $progresso->iniciado_em->format('d/m/Y H:i') }}
                                                    @endif
                                                </p>
                                            </div>
                                            <span class="text-sm font-semibold {{ $progresso->completo ? 'text-green-600' : 'text-yellow-600' }}">
                                                {{ $progresso->percentual }}%
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-{{ $progresso->completo ? 'green' : 'yellow' }}-500 h-2 rounded-full" 
                                                 style="width: {{ $progresso->percentual }}%"></div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $progresso->visualizacoes }} visualizações
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Coluna Lateral -->
                <div class="space-y-6">
                    <!-- Estatísticas -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Estatísticas</h3>
                        
                        <div class="space-y-4">
                            <div class="border-l-4 border-blue-500 pl-3">
                                <p class="text-2xl font-bold text-gray-900">{{ $totalAlunos }}</p>
                                <p class="text-sm text-gray-600">Total de Alunos</p>
                            </div>

                            <div class="border-l-4 border-yellow-500 pl-3">
                                <p class="text-2xl font-bold text-gray-900">{{ $alunosIniciaram }}</p>
                                <p class="text-sm text-gray-600">Iniciaram</p>
                            </div>

                            <div class="border-l-4 border-green-500 pl-3">
                                <p class="text-2xl font-bold text-gray-900">{{ $alunosConcluiram }}</p>
                                <p class="text-sm text-gray-600">Concluíram</p>
                            </div>

                            <div class="border-t pt-4">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Taxa de Conclusão</span>
                                    <span class="font-semibold">{{ $totalAlunos > 0 ? round(($alunosConcluiram / $totalAlunos) * 100) : 0 }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" 
                                         style="width: {{ $totalAlunos > 0 ? ($alunosConcluiram / $totalAlunos) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alunos com Acesso -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Alunos com Acesso</h3>
                        
                        @if($conteudo->alunos_ids && count($conteudo->alunos_ids) > 0)
                            <div class="space-y-2">
                                @foreach(\App\Models\Aluno::whereIn('id', $conteudo->alunos_ids)->get() as $aluno)
                                    <div class="flex items-center py-2 px-3 bg-gray-50 rounded-lg">
                                        <div class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center font-semibold text-sm">
                                            {{ strtoupper(substr($aluno->nome, 0, 1)) }}
                                        </div>
                                        <span class="ml-3 text-sm text-gray-700">{{ $aluno->nome }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">Nenhum aluno selecionado</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

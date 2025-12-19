@extends('layouts.app')

@section('content')
<div class="py-8">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Conteúdos Gravados
            </h2>
            <a href="{{ route('conteudos.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                + Novo Conteúdo
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if($conteudos->isEmpty())
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <div class="text-gray-400 mb-4">
                        <svg class="w-20 h-20 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Nenhum conteúdo cadastrado</h3>
                    <p class="text-gray-500 mb-6">Comece criando seu primeiro conteúdo para seus alunos</p>
                    <a href="{{ route('conteudos.create') }}" 
                       class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition">
                        Criar Primeiro Conteúdo
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($conteudos as $conteudo)
                        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition border-t-4 
                            {{ $conteudo->status === 'publicado' ? 'border-green-500' : 
                               ($conteudo->status === 'rascunho' ? 'border-yellow-500' : 'border-gray-400') }}">
                            <div class="p-6">
                                <!-- Tipo e Status -->
                                <div class="flex justify-between items-start mb-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold 
                                        {{ $conteudo->tipo === 'video' ? 'bg-blue-100 text-blue-800' : 
                                           ($conteudo->tipo === 'pdf' ? 'bg-red-100 text-red-800' : 
                                           ($conteudo->tipo === 'link' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ strtoupper($conteudo->tipo) }}
                                    </span>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $conteudo->status === 'publicado' ? 'bg-green-100 text-green-800' : 
                                           ($conteudo->status === 'rascunho' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst($conteudo->status) }}
                                    </span>
                                </div>

                                <!-- Título -->
                                <h3 class="text-lg font-bold text-gray-900 mb-2">
                                    {{ $conteudo->titulo }}
                                </h3>

                                <!-- Descrição -->
                                <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                                    {{ $conteudo->descricao ?? 'Sem descrição' }}
                                </p>

                                <!-- Informações -->
                                <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                                    <span>👥 {{ count($conteudo->alunos_ids ?? []) }} alunos</span>
                                    @if($conteudo->duracao_segundos)
                                        <span>⏱️ {{ $conteudo->duracao_formatada }}</span>
                                    @endif
                                </div>

                                <!-- Estatísticas de Progresso -->
                                @php
                                    $total = count($conteudo->alunos_ids ?? []);
                                    $iniciados = $conteudo->progressos->where('iniciado_em', '!=', null)->count();
                                    $concluidos = $conteudo->progressos->where('completo', true)->count();
                                @endphp
                                <div class="mb-4">
                                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                                        <span>Progresso dos alunos</span>
                                        <span>{{ $total > 0 ? round(($concluidos / $total) * 100) : 0 }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" 
                                             style="width: {{ $total > 0 ? ($concluidos / $total) * 100 : 0 }}%"></div>
                                    </div>
                                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                                        <span>{{ $iniciados }} iniciaram</span>
                                        <span>{{ $concluidos }} concluíram</span>
                                    </div>
                                </div>

                                <!-- Ações -->
                                <div class="flex gap-2">
                                    <a href="{{ route('conteudos.show', $conteudo) }}" 
                                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 rounded-lg transition text-sm">
                                        Ver Detalhes
                                    </a>
                                    <a href="{{ route('conteudos.edit', $conteudo) }}" 
                                       class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition text-sm">
                                        Editar
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginação -->
                <div class="mt-6">
                    {{ $conteudos->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

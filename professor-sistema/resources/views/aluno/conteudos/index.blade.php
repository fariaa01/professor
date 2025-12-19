<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Meus Conteúdos</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Meus Conteúdos</h1>
                    <a href="/dashboard" class="text-blue-600 hover:text-blue-800">← Voltar ao Dashboard</a>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @if($conteudos->isEmpty())
                    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                        <div class="text-gray-400 mb-4">
                            <svg class="w-20 h-20 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Nenhum conteúdo disponível</h3>
                        <p class="text-gray-500">Seu professor ainda não liberou nenhum conteúdo para você</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($conteudos as $conteudo)
                            @php
                                $progresso = $conteudo->progressos->first();
                                $percentual = $progresso ? $progresso->percentual : 0;
                                $completo = $progresso ? $progresso->completo : false;
                            @endphp

                            <a href="{{ route('aluno.conteudos.show', $conteudo) }}" 
                               class="bg-white rounded-lg shadow-sm hover:shadow-md transition border-t-4 
                                {{ $completo ? 'border-green-500' : ($percentual > 0 ? 'border-yellow-500' : 'border-blue-500') }} 
                                block">
                                <div class="p-6">
                                    <!-- Tipo e Status -->
                                    <div class="flex justify-between items-start mb-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold 
                                            {{ $conteudo->tipo === 'video' ? 'bg-blue-100 text-blue-800' : 
                                               ($conteudo->tipo === 'pdf' ? 'bg-red-100 text-red-800' : 
                                               ($conteudo->tipo === 'link' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ strtoupper($conteudo->tipo) }}
                                        </span>
                                        @if($completo)
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                ✓ Concluído
                                            </span>
                                        @elseif($percentual > 0)
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                Em andamento
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                Novo
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Título -->
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">
                                        {{ $conteudo->titulo }}
                                    </h3>

                                    <!-- Descrição -->
                                    <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                                        {{ $conteudo->descricao ?? 'Sem descrição' }}
                                    </p>

                                    <!-- Professor -->
                                    <div class="flex items-center text-xs text-gray-500 mb-4">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $conteudo->professor->name }}
                                        @if($conteudo->duracao_segundos)
                                            <span class="ml-3">⏱️ {{ $conteudo->duracao_formatada }}</span>
                                        @endif
                                    </div>

                                    <!-- Barra de Progresso -->
                                    @if($progresso)
                                        <div class="mb-3">
                                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                                <span>Seu progresso</span>
                                                <span>{{ $percentual }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-{{ $completo ? 'green' : 'blue' }}-500 h-2 rounded-full transition-all" 
                                                     style="width: {{ $percentual }}%"></div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Botão -->
                                    <div class="mt-4">
                                        <span class="text-blue-600 hover:text-blue-800 font-semibold text-sm inline-flex items-center">
                                            {{ $percentual > 0 ? 'Continuar assistindo' : 'Começar agora' }}
                                            <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </main>
    </div>
</body>
</html>

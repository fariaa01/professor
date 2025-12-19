@extends('layouts.app')

@section('title', $conteudo->titulo)

@section('content')
<div class="min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <a href="{{ route('aluno.conteudos.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                        ← Voltar para Conteúdos
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ $conteudo->titulo }}</h1>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-600">Seu progresso</div>
                    <div class="text-2xl font-bold {{ $progresso->completo ? 'text-green-600' : 'text-blue-600' }}">
                        {{ $progresso->percentual }}%
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Content -->
    <main class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Coluna Principal (Player) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Player de Vídeo -->
                    @if($conteudo->tipo === 'video' && $conteudo->url)
                        <div class="bg-black rounded-lg overflow-hidden shadow-lg">
                            <div class="relative" style="padding-top: 56.25%;">
                                @php
                                    $videoId = null;
                                    $platform = null;
                                    
                                    // Detecta YouTube
                                    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\?\/]+)/', $conteudo->url, $matches)) {
                                        $videoId = $matches[1];
                                        $platform = 'youtube';
                                    }
                                    // Detecta Vimeo
                                    elseif (preg_match('/vimeo\.com\/(\d+)/', $conteudo->url, $matches)) {
                                        $videoId = $matches[1];
                                        $platform = 'vimeo';
                                    }
                                @endphp

                                @if($platform === 'youtube')
                                    <iframe 
                                        id="video-player"
                                        class="absolute top-0 left-0 w-full h-full"
                                        src="https://www.youtube.com/embed/{{ $videoId }}?enablejsapi=1&start={{ $progresso->progresso_segundos }}"
                                        frameborder="0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                        allowfullscreen>
                                    </iframe>
                                @elseif($platform === 'vimeo')
                                    <iframe 
                                        id="video-player"
                                        class="absolute top-0 left-0 w-full h-full"
                                        src="https://player.vimeo.com/video/{{ $videoId }}#t={{ $progresso->progresso_segundos }}s"
                                        frameborder="0" 
                                        allow="autoplay; fullscreen; picture-in-picture" 
                                        allowfullscreen>
                                    </iframe>
                                @else
                                    <div class="absolute top-0 left-0 w-full h-full flex items-center justify-center bg-gray-900 text-white">
                                        <div class="text-center">
                                            <p class="mb-4">Player não suportado para este formato</p>
                                            <a href="{{ $conteudo->url }}" target="_blank" 
                                               class="bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded-lg">
                                                Abrir em Nova Aba
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @elseif($conteudo->tipo === 'link')
                        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                            <svg class="w-16 h-16 mx-auto text-blue-500 mb-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"></path>
                            </svg>
                            <h3 class="text-lg font-semibold mb-4">Link Externo</h3>
                            <a href="{{ $conteudo->url }}" target="_blank" 
                               class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition">
                                Acessar Conteúdo
                            </a>
                        </div>
                    @endif

                    <!-- Descrição -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Sobre este conteúdo</h2>
                        <p class="text-gray-700 whitespace-pre-line">{{ $conteudo->descricao ?? 'Sem descrição disponível' }}</p>
                    </div>

                    <!-- Observações do Professor -->
                    @if($conteudo->observacoes)
                        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6">
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <h3 class="font-semibold text-blue-900 mb-2">Observações do Professor</h3>
                                    <p class="text-blue-800 whitespace-pre-line">{{ $conteudo->observacoes }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Informações -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">Informações</h3>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <div class="text-gray-600">Professor</div>
                                    <div class="font-medium text-gray-900">{{ $conteudo->professor->name }}</div>
                                </div>
                            </div>

                            @if($conteudo->duracao_segundos)
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div>
                                        <div class="text-gray-600">Duração</div>
                                        <div class="font-medium text-gray-900">{{ $conteudo->duracao_formatada }} minutos</div>
                                    </div>
                                </div>
                            @endif

                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <div class="text-gray-600">Tipo</div>
                                    <div class="font-medium text-gray-900">{{ ucfirst($conteudo->tipo) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progresso -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">Seu Progresso</h3>
                        
                        <div class="mb-4">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">Completo</span>
                                <span class="font-semibold text-gray-900">{{ $progresso->percentual }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-{{ $progresso->completo ? 'green' : 'blue' }}-500 h-3 rounded-full transition-all" 
                                     style="width: {{ $progresso->percentual }}%"></div>
                            </div>
                        </div>

                        <div class="space-y-2 text-sm">
                            @if($progresso->iniciado_em)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Iniciado em:</span>
                                    <span class="font-medium">{{ $progresso->iniciado_em->format('d/m/Y') }}</span>
                                </div>
                            @endif

                            @if($progresso->completo && $progresso->concluido_em)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Concluído em:</span>
                                    <span class="font-medium text-green-600">{{ $progresso->concluido_em->format('d/m/Y') }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between">
                                <span class="text-gray-600">Visualizações:</span>
                                <span class="font-medium">{{ $progresso->visualizacoes }}</span>
                            </div>
                        </div>

                        @if($progresso->completo)
                            <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                                <svg class="w-8 h-8 text-green-500 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-green-800 font-semibold">Parabéns!</p>
                                <p class="text-green-700 text-sm">Você concluiu este conteúdo</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    // Salvar progresso do vídeo a cada 10 segundos
    let progressoInterval;
    let currentTime = {{ $progresso->progresso_segundos }};

    function salvarProgresso(segundos) {
        fetch('{{ route('aluno.conteudos.progresso', $conteudo) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ progresso_segundos: Math.floor(segundos) })
        });
    }

    // Se for vídeo do YouTube, monitorar progresso
    @if($platform === 'youtube' && $videoId)
        // YouTube Player API
        var tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        var player;
        function onYouTubeIframeAPIReady() {
            player = new YT.Player('video-player', {
                events: {
                    'onStateChange': onPlayerStateChange
                }
            });
        }

        function onPlayerStateChange(event) {
            if (event.data == YT.PlayerState.PLAYING) {
                progressoInterval = setInterval(() => {
                    salvarProgresso(player.getCurrentTime());
                }, 10000); // A cada 10 segundos
            } else {
                clearInterval(progressoInterval);
                if (player && player.getCurrentTime) {
                    salvarProgresso(player.getCurrentTime());
                }
            }
        }

        // Salvar ao sair da página
        window.addEventListener('beforeunload', () => {
            if (player && player.getCurrentTime) {
                salvarProgresso(player.getCurrentTime());
            }
        });
    @endif
</script>
@endsection

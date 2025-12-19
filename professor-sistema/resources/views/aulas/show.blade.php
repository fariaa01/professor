<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header com Ações -->
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <x-button variant="ghost" :href="route('aulas.index')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </x-button>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Registro da Aula</h1>
                    <p class="mt-1 text-sm text-gray-600">Detalhes completos e anotações pedagógicas</p>
                </div>
            </div>
            @if($aula->status === 'realizada')
                <x-button variant="primary" :href="route('aulas.edit', $aula)">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editar Registro
                </x-button>
            @endif
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Informações Básicas da Aula -->
        <x-card class="mb-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $aula->aluno->nome }}</h2>
                    <p class="text-sm text-gray-600 mt-1">{{ $aula->data_hora->format('d/m/Y') }} às {{ $aula->data_hora->format('H:i') }}</p>
                </div>
                <div class="flex gap-2">
                    <x-badge :variant="$aula->status === 'realizada' ? 'success' : ($aula->status === 'agendada' ? 'info' : 'warning')">
                        @if($aula->status === 'cancelada_aluno') Falta Aluno
                        @else {{ ucfirst($aula->status) }}
                        @endif
                    </x-badge>
                    @if($aula->status_pagamento)
                        <x-badge :variant="$aula->status_pagamento === 'pago' ? 'success' : ($aula->status_pagamento === 'atrasado' ? 'danger' : 'warning')">
                            {{ ucfirst($aula->status_pagamento) }}
                        </x-badge>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                <div class="p-3 bg-blue-50 rounded-lg">
                    <p class="text-xs text-blue-600 font-medium mb-1">Duração</p>
                    <p class="text-lg font-bold text-gray-900">{{ $aula->duracao_minutos }} minutos</p>
                </div>
                @if($aula->valor)
                    <div class="p-3 bg-green-50 rounded-lg">
                        <p class="text-xs text-green-600 font-medium mb-1">Valor</p>
                        <p class="text-lg font-bold text-gray-900">R$ {{ number_format($aula->valor, 2, ',', '.') }}</p>
                    </div>
                @endif
                @if($aula->forma_pagamento)
                    <div class="p-3 bg-purple-50 rounded-lg">
                        <p class="text-xs text-purple-600 font-medium mb-1">Forma de Pagamento</p>
                        <p class="text-lg font-bold text-gray-900">{{ ucfirst($aula->forma_pagamento) }}</p>
                    </div>
                @endif
            </div>
        </x-card>

        <!-- Conteúdo Pedagógico -->
        @if($aula->status === 'realizada')
            <div class="space-y-6">
                <!-- Conteúdo da Aula -->
                @if($aula->conteudo)
                    <x-card>
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">Conteúdo Trabalhado</h3>
                        </div>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $aula->conteudo }}</p>
                    </x-card>
                @endif

                <!-- Materiais Utilizados -->
                @if($aula->materiais)
                    <x-card>
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">Materiais Utilizados</h3>
                        </div>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $aula->materiais }}</p>
                    </x-card>
                @endif

                <!-- Exercícios Aplicados -->
                @if($aula->exercicios)
                    <x-card>
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">Exercícios Aplicados</h3>
                        </div>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $aula->exercicios }}</p>
                    </x-card>
                @endif

                <!-- Dificuldades Encontradas -->
                @if($aula->dificuldades)
                    <x-card>
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">Dificuldades Encontradas</h3>
                        </div>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $aula->dificuldades }}</p>
                    </x-card>
                @endif

                <!-- Pontos de Atenção -->
                @if($aula->pontos_atencao)
                    <x-card>
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">Pontos de Atenção</h3>
                        </div>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $aula->pontos_atencao }}</p>
                    </x-card>
                @endif

                <!-- Observações Gerais -->
                @if($aula->observacoes)
                    <x-card>
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">Observações Gerais</h3>
                        </div>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $aula->observacoes }}</p>
                    </x-card>
                @endif

                <!-- Mensagem se não houver registro -->
                @if(!$aula->conteudo && !$aula->materiais && !$aula->exercicios && !$aula->dificuldades && !$aula->pontos_atencao && !$aula->observacoes)
                    <x-card>
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum registro pedagógico</h3>
                            <p class="mt-1 text-sm text-gray-500">Clique em "Editar Registro" para adicionar informações sobre esta aula.</p>
                            <div class="mt-6">
                                <x-button variant="primary" :href="route('aulas.edit', $aula)">
                                    Adicionar Registro
                                </x-button>
                            </div>
                        </div>
                    </x-card>
                @endif
            </div>
        @else
            <x-card>
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aula não realizada</h3>
                    <p class="mt-1 text-sm text-gray-500">O registro pedagógico estará disponível após a aula ser marcada como realizada.</p>
                </div>
            </x-card>
        @endif
    </div>
</x-app-layout>

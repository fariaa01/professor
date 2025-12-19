@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Aulas e Registros</h1>
            <p class="mt-1 text-sm text-gray-600">Acompanhamento pedagógico e histórico de conteúdos</p>
        </div>

        <!-- Estatísticas Rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <x-stat-card 
                title="Total de Aulas" 
                :value="$totalAulas"
                :icon="'<svg class=\'w-6 h-6 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2\'></path></svg>'"
            />
            
            <x-stat-card 
                title="Aulas Realizadas" 
                :value="$aulasRealizadas"
                :icon="'<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
            />
            
            <x-stat-card 
                title="Próximas Aulas" 
                :value="$proximasAulas"
                :icon="'<svg class=\'w-6 h-6 text-purple-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg>'"
            />
        </div>

        <!-- Filtros -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('aulas.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select 
                        name="status"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos os status</option>
                        <option value="agendada" {{ request('status') === 'agendada' ? 'selected' : '' }}>Agendada</option>
                        <option value="realizada" {{ request('status') === 'realizada' ? 'selected' : '' }}>Realizada</option>
                        <option value="cancelada_aluno" {{ request('status') === 'cancelada_aluno' ? 'selected' : '' }}>Falta Aluno</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Aluno</label>
                    <select 
                        name="aluno_id"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos os alunos</option>
                        @foreach($alunos as $aluno)
                            <option value="{{ $aluno->id }}" {{ request('aluno_id') == $aluno->id ? 'selected' : '' }}>
                                {{ $aluno->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data</label>
                    <input 
                        type="date" 
                        name="data" 
                        value="{{ request('data') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="flex items-end gap-2">
                    <x-button variant="primary" type="submit" class="flex-1">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Filtrar
                    </x-button>
                    @if(request()->hasAny(['status', 'aluno_id', 'data']))
                        <x-button variant="ghost" :href="route('aulas.index')">
                            Limpar
                        </x-button>
                    @endif
                </div>
            </form>
        </x-card>

        <!-- Lista de Aulas -->
        <x-card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Histórico de Aulas</h3>
                <span class="text-sm text-gray-500">{{ $aulas->total() }} aulas encontradas</span>
            </div>

            @if($aulas->count() > 0)
                <div class="space-y-3">
                    @foreach($aulas as $aula)
                        <div class="p-4 bg-gray-50 rounded-lg border-l-4 hover:bg-gray-100 transition-colors
                            @if($aula->status === 'realizada') border-green-500
                            @elseif($aula->status === 'agendada') border-blue-500
                            @else border-yellow-500
                            @endif
                        ">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h4 class="font-semibold text-gray-900 text-lg">{{ $aula->aluno->nome }}</h4>
                                        <x-badge :variant="$aula->status === 'realizada' ? 'success' : ($aula->status === 'agendada' ? 'info' : 'warning')">
                                            @if($aula->status === 'cancelada_aluno') Falta
                                            @else {{ ucfirst($aula->status) }}
                                            @endif
                                        </x-badge>
                                        @if($aula->status_pagamento)
                                            <x-badge :variant="$aula->status_pagamento === 'pago' ? 'success' : ($aula->status_pagamento === 'atrasado' ? 'danger' : 'warning')">
                                                {{ ucfirst($aula->status_pagamento) }}
                                            </x-badge>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm text-gray-600 mb-2">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span>{{ $aula->data_hora->format('d/m/Y') }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>{{ $aula->data_hora->format('H:i') }} ({{ $aula->duracao_minutos }}min)</span>
                                        </div>
                                        @if($aula->valor)
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="font-medium text-green-600">R$ {{ number_format($aula->valor, 2, ',', '.') }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    @if($aula->conteudo)
                                        <div class="mb-2">
                                            <p class="text-sm text-gray-700">
                                                <span class="font-medium">Conteúdo:</span> 
                                                {{ Str::limit($aula->conteudo, 150) }}
                                            </p>
                                        </div>
                                    @endif

                                    <div class="flex items-center gap-2 mt-3">
                                        @if($aula->materiais || $aula->exercicios || $aula->dificuldades)
                                            <div class="flex items-center gap-1 text-xs text-gray-500">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                Registro completo disponível
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2 ml-4">
                                    <x-button variant="outline" :href="route('aulas.show', $aula)" size="sm">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Ver
                                    </x-button>
                                    @if($aula->status === 'realizada')
                                        <x-button variant="ghost" :href="route('aulas.edit', $aula)" size="sm">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Editar
                                        </x-button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginação -->
                <div class="mt-6">
                    {{ $aulas->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma aula encontrada</h3>
                    <p class="mt-1 text-sm text-gray-500">Tente ajustar os filtros ou aguarde novas aulas serem registradas.</p>
                </div>
            @endif
        </x-card>
    </div>
</div>
@endsection

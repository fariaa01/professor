@extends('layouts.app')

@section('content')
<div class="py-8" x-data="{ createMeeting: false, createAula: false, createAluno: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                <p class="mt-1 text-sm text-gray-600">Visão geral da sua rotina de aulas</p>
            </div>
            <div class="flex gap-3">
                     <button type="button" @click="createMeeting = true"
                         class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow-sm transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    Iniciar Reunião
                     </button>
                     <a href="{{ route('meetings.index') }}" 
                         class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Ver Reuniões
                </a>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <x-stat-card 
                title="Alunos Ativos" 
                :value="$alunosAtivos"
                :icon="'<svg class=\'w-6 h-6 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z\'></path></svg>'"
            />
            
            <x-stat-card 
                title="Aulas Realizadas" 
                :value="$aulasRealizadas"
                :icon="'<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
            />
            
            <x-stat-card 
                title="Faltas (Aluno)" 
                :value="$faltasAluno"
                :icon="'<svg class=\'w-6 h-6 text-yellow-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z\'></path></svg>'"
            />
            
            <x-stat-card 
                title="Carga Horária" 
                :value="number_format($cargaHoraria / 60, 1) . 'h'"
                :icon="'<svg class=\'w-6 h-6 text-purple-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
            />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Calendário da Semana -->
            <div class="lg:col-span-2">
                <x-card>
                    <x-card-header title="Calendário da Semana" 
                        description="{{ \Carbon\Carbon::now()->startOfWeek()->format('d/m') }} - {{ \Carbon\Carbon::now()->endOfWeek()->format('d/m/Y') }}" 
                    />
                    
                    <div class="space-y-3">
                        @php
                            $diasSemana = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];
                            $hoje = \Carbon\Carbon::now();
                        @endphp
                        
                        @for ($i = 0; $i < 7; $i++)
                            @php
                                $dia = $hoje->copy()->startOfWeek()->addDays($i);
                                $aulasNoDia = $aulasSemana->filter(function($aula) use ($dia) {
                                    return $aula->data_hora->format('Y-m-d') === $dia->format('Y-m-d');
                                });
                            @endphp
                            
                            <div class="border border-gray-200 rounded-lg p-4 {{ $dia->isToday() ? 'bg-blue-50 border-blue-300' : 'bg-white' }}">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $diasSemana[$i] }}</h4>
                                        <p class="text-sm text-gray-500">{{ $dia->format('d/m') }}</p>
                                    </div>
                                    @if($dia->isToday())
                                        <x-badge variant="info">Hoje</x-badge>
                                    @endif
                                </div>
                                
                                @if($aulasNoDia->count() > 0)
                                    <div class="space-y-2">
                                        @foreach($aulasNoDia as $aula)
                                            @if(!isset($aula->is_recorrente))
                                            <div 
                                                x-data="{ open: false }" 
                                                class="flex items-center justify-between p-3 bg-white rounded border-l-4 
                                                @if($aula->status === 'agendada') border-blue-500
                                                @elseif($aula->status === 'realizada') border-green-500
                                                @else border-yellow-500
                                                @endif
                                                aula-item-{{ $aula->id }}
                                            ">
                                                <div class="flex-1">
                                                    <p class="font-medium text-gray-900">{{ $aula->aluno->nome }}</p>
                                                    <p class="text-sm text-gray-600">{{ $aula->data_hora->format('H:i') }} - {{ $aula->duracao_minutos }}min</p>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="badge-status-{{ $aula->id }}">
                                                        <x-badge 
                                                            :variant="$aula->status === 'agendada' ? 'info' : ($aula->status === 'realizada' ? 'success' : 'warning')"
                                                        >
                                                            @if($aula->status === 'cancelada_aluno') Falta Aluno
                                                            @else {{ ucfirst($aula->status) }}
                                                            @endif
                                                        </x-badge>
                                                    </span>
                                                    
                                                    <!-- Dropdown de Status -->
                                                    <div class="relative" @click.away="open = false">
                                                        <button 
                                                            type="button"
                                                            @click="open = !open"
                                                            class="p-1 rounded hover:bg-gray-100 transition-colors">
                                                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                                            </svg>
                                                        </button>
                                                        
                                                        <div 
                                                            x-show="open"
                                                            x-transition
                                                            class="absolute right-0 mt-2 w-52 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10">
                                                            <div class="px-3 py-2 border-b border-gray-100">
                                                                <p class="text-xs font-semibold text-gray-500 uppercase">Alterar Status</p>
                                                            </div>
                                                            <button 
                                                                @click="updateAulaStatus({{ $aula->id }}, 'realizada'); open = false"
                                                                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 flex items-center gap-2">
                                                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                                                Realizada
                                                            </button>
                                                            <button 
                                                                @click="updateAulaStatus({{ $aula->id }}, 'cancelada_aluno'); open = false"
                                                                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 flex items-center gap-2">
                                                                <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                                                                Falta Aluno
                                                            </button>
                                                            <button 
                                                                @click="updateAulaStatus({{ $aula->id }}, 'agendada'); open = false"
                                                                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 flex items-center gap-2">
                                                                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                                                Agendada
                                                            </button>
                                                            
                                                            <div class="border-t border-gray-100 mt-1 pt-1">
                                                                <div class="px-3 py-2">
                                                                    <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Reagendar</p>
                                                                </div>
                                                                <button 
                                                                    @click="openRescheduleModal({{ $aula->id }}, '{{ $aula->data_hora->format('Y-m-d\TH:i') }}'); open = false"
                                                                    class="w-full text-left px-4 py-2 text-sm hover:bg-blue-50 text-blue-600 flex items-center gap-2">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                    </svg>
                                                                    Alterar Data/Hora
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @else
                                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded border-l-4 border-blue-300 opacity-75">
                                                <div class="flex-1">
                                                    <p class="font-medium text-gray-900">{{ $aula->aluno->nome }}</p>
                                                    <p class="text-sm text-gray-600">{{ $aula->data_hora->format('H:i') }} - {{ $aula->duracao_minutos }}min</p>
                                                </div>
                                                <x-badge variant="secondary">Horário Fixo</x-badge>
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500 italic">Nenhuma aula agendada</p>
                                @endif
                            </div>
                        @endfor
                    </div>
                </x-card>
            </div>

            <!-- Próximas Aulas -->
            <div class="lg:col-span-1">
                <x-card>
                    <x-card-header title="Próximas Aulas" description="Próximos 7 dias" />
                    
                    @if($proximasAulas->count() > 0)
                        <div class="space-y-3">
                            @foreach($proximasAulas as $aula)
                                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-blue-300 transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900">{{ $aula->aluno->nome }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                {{ $aula->data_hora->format('d/m/Y') }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $aula->data_hora->format('H:i') }} ({{ $aula->duracao_minutos }}min)
                                            </p>
                                        </div>
                                    </div>
                                    
                                    @if($aula->observacoes)
                                        <p class="text-xs text-gray-500 mt-2 italic">{{ Str::limit($aula->observacoes, 50) }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-gray-500">Nenhuma aula agendada</p>
                            <x-button variant="primary" size="sm" class="mt-4">
                                Agendar Aula
                            </x-button>
                        </div>
                    @endif
                </x-card>

                <!-- Card de Ações Rápidas -->
                <x-card class="mt-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Ações Rápidas</h3>
                    <div class="space-y-2">
                        <x-button variant="outline" type="button" @click="createAula = true" class="w-full justify-start">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Nova Aula
                        </x-button>
                        <x-button variant="outline" type="button" @click="createAluno = true" class="w-full justify-start">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            Novo Aluno
                        </x-button>
                        <x-button variant="outline" :href="route('relatorios.index')" class="w-full justify-start">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Ver Relatórios
                        </x-button>
                    </div>
                </x-card>
            </div>
        </div>
    </div>

    <!-- Modal de Reagendamento -->
    <div 
        x-data="{ show: false, aulaId: null, dataHora: '' }" 
        x-show="show"
        @open-reschedule-modal.window="show = true; aulaId = $event.detail.aulaId; dataHora = $event.detail.dataHora"
        @close-reschedule-modal.window="show = false"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;">
        <!-- Overlay -->
        <div 
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black bg-opacity-50"
            @click="show = false">
        </div>

        <!-- Modal -->
        <div class="flex items-center justify-center min-h-screen px-4">
            <div 
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="bg-white rounded-lg shadow-xl w-full max-w-md sm:max-w-lg lg:max-w-xl p-4 sm:p-6 relative z-10 max-h-[80vh] overflow-y-auto mx-4">
                
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Reagendar Aula</h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nova Data e Hora</label>
                    <input 
                        type="datetime-local" 
                        x-model="dataHora"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                    <p class="text-sm text-blue-800">
                        <strong>Nota:</strong> Apenas esta aula será reagendada. Os horários fixos do aluno não serão alterados.
                    </p>
                </div>

                <div class="flex justify-end gap-3">
                    <button 
                        type="button"
                        @click="show = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button 
                        type="button"
                        @click="rescheduleAula(aulaId, dataHora); show = false"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                        Confirmar Reagendamento
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Criar Reunião -->
    <div x-show="createMeeting" x-cloak x-data="{ scroll: true }" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4">
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="createMeeting = false"></div>
        <div :class="scroll ? 'bg-white rounded-lg shadow-lg w-full max-w-md sm:max-w-lg lg:max-w-xl p-4 sm:p-6 z-10 max-h-[calc(100vh-4rem)] overflow-y-auto' : 'bg-white rounded-lg shadow-lg w-full max-w-md sm:max-w-lg lg:max-w-xl p-4 sm:p-6 z-10'">
            <h3 class="text-lg font-semibold mb-4">Nova Reunião</h3>
            <form @if(Route::has('meetings.store')) action="{{ route('meetings.store') }}" @else action="#" data-no-route="true" @endif onsubmit="event.preventDefault(); submitCreateMeeting(this);">
                @csrf
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Título</label>
                    <input name="titulo" type="text" required class="w-full rounded border-gray-300 p-2">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea name="descricao" class="w-full rounded border-gray-300 p-2"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="createMeeting = false" class="px-4 py-2 border rounded">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Criar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Criar Aula -->
    <div x-show="createAula" x-cloak x-data="{ scroll: true }" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4">
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="createAula = false"></div>
        <div :class="scroll ? 'bg-white rounded-lg shadow-lg w-full max-w-md sm:max-w-lg lg:max-w-xl p-4 sm:p-6 z-10 max-h-[calc(100vh-4rem)] overflow-y-auto' : 'bg-white rounded-lg shadow-lg w-full max-w-md sm:max-w-lg lg:max-w-xl p-4 sm:p-6 z-10'">
            <h3 class="text-lg font-semibold mb-4">Nova Aula</h3>
            <div class="flex justify-end mb-2">
                <button type="button" @click="scroll = !scroll" class="text-sm text-gray-600 hover:text-gray-800">Alternar scroll</button>
            </div>
            <form @if(Route::has('aulas.store')) action="{{ route('aulas.store') }}" @else action="#" data-no-route="true" @endif onsubmit="event.preventDefault(); submitCreateAula(this);">
                @csrf
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Aluno</label>
                    <select name="aluno_id" class="w-full rounded border-gray-300 p-2">
                        @foreach($alunos as $aluno)
                            <option value="{{ $aluno->id }}">{{ $aluno->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Data e Hora</label>
                    <input name="data_hora" type="datetime-local" required class="w-full rounded border-gray-300 p-2">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Duração (min)</label>
                    <input name="duracao_minutos" type="number" min="1" value="60" class="w-full rounded border-gray-300 p-2">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="createAula = false" class="px-4 py-2 border rounded">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Criar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Criar Aluno -->
    <div x-show="createAluno" x-cloak class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 sm:p-6">
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="createAluno = false"></div>
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md sm:max-w-lg lg:max-w-xl p-4 sm:p-6 z-10 h-[80vh] flex flex-col">
            <h3 class="text-lg font-semibold mb-4">Novo Aluno</h3>
            <form @if(Route::has('alunos.store')) action="{{ route('alunos.store') }}" @else action="#" data-no-route="true" @endif x-data="horariosManager()" onsubmit="event.preventDefault(); submitCreateAluno(this);">
                <div class="overflow-y-auto flex-1 min-h-0 pr-2 pb-4" style="max-height: calc(80vh - 7.5rem); overflow-y: auto; -webkit-overflow-scrolling: touch;">
                @csrf

                <!-- Informações Básicas -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informações Básicas</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <x-input-label for="nome" value="Nome Completo *" />
                            <x-input id="nome" name="nome" type="text" required autofocus />
                            <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" value="E-mail" />
                            <x-input id="email" name="email" type="email" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="telefone" value="Telefone" />
                            <x-input id="telefone" name="telefone" type="text" placeholder="(00) 00000-0000" />
                            <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="endereco" value="Endereço" />
                            <x-input id="endereco" name="endereco" type="text" />
                            <x-input-error :messages="$errors->get('endereco')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 my-6"></div>

                <!-- Responsável (opcional) -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Responsável (opcional)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="responsavel" value="Nome do Responsável" />
                            <x-input id="responsavel" name="responsavel" type="text" />
                            <x-input-error :messages="$errors->get('responsavel')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="telefone_responsavel" value="Telefone do Responsável" />
                            <x-input id="telefone_responsavel" name="telefone_responsavel" type="text" placeholder="(00) 00000-0000" />
                            <x-input-error :messages="$errors->get('telefone_responsavel')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 my-6"></div>

                <!-- Informações da Aula -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informações da Aula</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="valor_aula" value="Valor da Aula (R$)" />
                            <x-input id="valor_aula" name="valor_aula" :money="true" placeholder="0,00" />
                            <x-input-error :messages="$errors->get('valor_aula')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="data_inicio" value="Data de Início" />
                            <x-input id="data_inicio" name="data_inicio" type="date" />
                            <x-input-error :messages="$errors->get('data_inicio')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 my-6"></div>

                <!-- Observações -->
                <div class="mb-6">
                    <x-input-label for="observacoes" value="Observações" />
                    <x-textarea id="observacoes" name="observacoes" rows="4" placeholder="Informações adicionais sobre o aluno..."></x-textarea>
                    <x-input-error :messages="$errors->get('observacoes')" class="mt-2" />
                </div>

                <div class="border-t border-gray-200 my-6"></div>

                <!-- Tags/Etiquetas -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tags</h3>
                    <p class="text-sm text-gray-600 mb-3">Selecione as etiquetas para categorizar o aluno</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($tags as $tag)
                            <label class="flex items-center p-3 bg-gray-50 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                                <input 
                                    type="checkbox" 
                                    name="tags[]" 
                                    value="{{ $tag->id }}"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                >
                                <span class="ml-3 flex items-center gap-2">
                                    <span 
                                        class="inline-block w-3 h-3 rounded-full" 
                                        style="background-color: {{ $tag->cor }};"
                                    ></span>
                                    <span class="text-sm font-medium text-gray-700">{{ $tag->nome }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                </div>

                <div class="border-t border-gray-200 my-6"></div>

                <!-- Horários das Aulas -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Horários das Aulas</h3>
                            <p class="mt-1 text-sm text-gray-600">Defina os dias e horários das aulas semanais</p>
                        </div>
                        <x-button type="button" variant="outline" @click="adicionarHorario()">
                            + Adicionar Horário
                        </x-button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(horario, index) in horarios" :key="index">
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Dia da Semana</label>
                                        <select 
                                            x-bind:id="'dia_semana_' + index" 
                                            x-bind:name="'horarios[' + index + '][dia_semana]'"
                                            x-model="horario.dia_semana"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            required>
                                            <option value="">Selecione...</option>
                                            <option value="0">Domingo</option>
                                            <option value="1">Segunda-feira</option>
                                            <option value="2">Terça-feira</option>
                                            <option value="3">Quarta-feira</option>
                                            <option value="4">Quinta-feira</option>
                                            <option value="5">Sexta-feira</option>
                                            <option value="6">Sábado</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Hora Início</label>
                                        <input 
                                            type="time" 
                                            x-bind:id="'hora_inicio_' + index"
                                            x-bind:name="'horarios[' + index + '][hora_inicio]'"
                                            x-model="horario.hora_inicio"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Hora Fim</label>
                                        <input 
                                            type="time" 
                                            x-bind:id="'hora_fim_' + index"
                                            x-bind:name="'horarios[' + index + '][hora_fim]'"
                                            x-model="horario.hora_fim"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            required>
                                    </div>

                                    <div class="flex items-end">
                                        <button 
                                            type="button" 
                                            @click="removerHorario(index)"
                                            class="w-full px-4 py-2 bg-red-50 text-red-600 rounded-md hover:bg-red-100 transition-colors">
                                            Remover
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="horarios.length === 0" class="text-center py-8 text-gray-500">
                            Nenhum horário cadastrado. Clique em "Adicionar Horário" para começar.
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="ativo" value="1" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Aluno ativo</span>
                    </label>
                </div>

                </div>
                <!-- Botões -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <x-button variant="outline" type="button" @click="createAluno = false">
                        Cancelar
                    </x-button>
                    <x-button variant="primary" type="submit">
                        Cadastrar Aluno
                    </x-button>
                </div>
            </form>
        </div>
    </div>

    </div>
</div>
@endsection

@push('scripts')
    <script>
        // Notificação toast
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white transition-opacity duration-300 z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Função para atualizar status da aula
        async function updateAulaStatus(aulaId, status) {
            try {
                const response = await fetch(`/aulas/${aulaId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ status })
                });

                const data = await response.json();

                if (data.success) {
                    // Atualizar badge de status
                    const badgeContainer = document.querySelector(`.badge-status-${aulaId}`);
                    if (badgeContainer) {
                        const variantClasses = {
                            'info': 'bg-blue-100 text-blue-800',
                            'success': 'bg-green-100 text-green-800',
                            'warning': 'bg-yellow-100 text-yellow-800',
                            'danger': 'bg-red-100 text-red-800',
                        };
                        
                        const variant = data.aula.status_variant;
                        const oldClasses = Object.values(variantClasses).join(' ');
                        
                        const badge = badgeContainer.querySelector('.inline-flex');
                        if (badge) {
                            badge.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${variantClasses[variant]}`;
                            badge.textContent = data.aula.status_label;
                        }
                    }

                    // Atualizar borda do card
                    const aulaItem = document.querySelector(`.aula-item-${aulaId}`);
                    if (aulaItem) {
                        aulaItem.className = aulaItem.className.replace(/border-(blue|green|yellow|red)-\d+/, '');
                        
                        const borderColors = {
                            'agendada': 'border-blue-500',
                            'realizada': 'border-green-500',
                            'cancelada_aluno': 'border-yellow-500',
                        };
                        
                        aulaItem.className += ` ${borderColors[status]}`;
                        
                        // Animação de sucesso
                        aulaItem.style.transform = 'scale(0.98)';
                        setTimeout(() => {
                            aulaItem.style.transform = 'scale(1)';
                        }, 100);
                    }

                    showToast(data.message, 'success');
                } else {
                    showToast('Erro ao atualizar status', 'error');
                }
            } catch (error) {
                console.error('Erro:', error);
                showToast('Erro ao atualizar status', 'error');
            }
        }

        // Função para abrir modal de reagendamento
        function openRescheduleModal(aulaId, dataHora) {
            window.dispatchEvent(new CustomEvent('open-reschedule-modal', {
                detail: { aulaId, dataHora }
            }));
        }

        // Função para reagendar aula
        async function rescheduleAula(aulaId, dataHora) {
            if (!dataHora) {
                showToast('Por favor, selecione uma data e hora', 'error');
                return;
            }

            try {
                const response = await fetch(`/aulas/${aulaId}/reschedule`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ data_hora: dataHora })
                });

                const data = await response.json();

                if (data.success) {
                    showToast(data.message, 'success');
                    
                    // Recarregar a página após 1 segundo para mostrar a aula na nova data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showToast(data.message || 'Erro ao reagendar aula', 'error');
                }
            } catch (error) {
                console.error('Erro:', error);
                showToast('Erro ao reagendar aula', 'error');
            }
        }

        // Tornar funções globais
        window.updateAulaStatus = updateAulaStatus;
        window.openRescheduleModal = openRescheduleModal;
        window.rescheduleAula = rescheduleAula;
    </script>
    <!-- Modais de Criação -->
    <script>
        async function submitCreateMeeting(formEl) {
            if (formEl.dataset.noRoute === 'true' || !formEl.action || formEl.action === '#') {
                showToast('Rota de criação de reuniões não está disponível', 'error');
                return;
            }

            try {
                const form = new FormData(formEl);
                const token = document.querySelector('meta[name="csrf-token"]').content;

                const response = await fetch(formEl.action, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                    body: form
                });

                const data = await response.json();
                if (data.success || response.ok) {
                    document.querySelector('[x-data]').__x.$data.createMeeting = false;
                    showToast(data.message || 'Reunião criada', 'success');
                } else {
                    showToast(data.message || 'Erro ao criar reunião', 'error');
                }
            } catch (e) {
                console.error(e);
                showToast('Erro ao criar reunião', 'error');
            }
        }

        async function submitCreateAula(formEl) {
            if (formEl.dataset.noRoute === 'true' || !formEl.action || formEl.action === '#') {
                showToast('Rota de criação de aulas não está disponível', 'error');
                return;
            }

            try {
                const form = new FormData(formEl);
                const token = document.querySelector('meta[name="csrf-token"]').content;

                const response = await fetch(formEl.action, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                    body: form
                });

                const data = await response.json();
                if (data.success || response.ok) {
                    document.querySelector('[x-data]').__x.$data.createAula = false;
                    showToast(data.message || 'Aula criada', 'success');
                } else {
                    showToast(data.message || 'Erro ao criar aula', 'error');
                }
            } catch (e) {
                console.error(e);
                showToast('Erro ao criar aula', 'error');
            }
        }

        async function submitCreateAluno(formEl) {
            if (formEl.dataset.noRoute === 'true' || !formEl.action || formEl.action === '#') {
                showToast('Rota de criação de alunos não está disponível', 'error');
                return;
            }

            try {
                const form = new FormData(formEl);
                const token = document.querySelector('meta[name="csrf-token"]').content;

                const response = await fetch(formEl.action, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                    body: form
                });

                const data = await response.json();
                if (data.success || response.ok) {
                    document.querySelector('[x-data]').__x.$data.createAluno = false;
                    showToast(data.message || 'Aluno criado', 'success');
                } else {
                    showToast(data.message || 'Erro ao criar aluno', 'error');
                }
            } catch (e) {
                console.error(e);
                showToast('Erro ao criar aluno', 'error');
            }
        }
    </script>
    <script>
        function horariosManager() {
            return {
                horarios: [],
                adicionarHorario() {
                    this.horarios.push({
                        dia_semana: '',
                        hora_inicio: '',
                        hora_fim: ''
                    });
                },
                removerHorario(index) {
                    this.horarios.splice(index, 1);
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Máscara para telefone
            const telefoneInput = document.getElementById('telefone');
            if (telefoneInput && typeof IMask !== 'undefined') {
                IMask(telefoneInput, {
                    mask: '(00) 00000-0000'
                });
            }

            // Máscara para telefone do responsável
            const telefoneResponsavelInput = document.getElementById('telefone_responsavel');
            if (telefoneResponsavelInput && typeof IMask !== 'undefined') {
                IMask(telefoneResponsavelInput, {
                    mask: '(00) 00000-0000'
                });
            }

            // Máscara para valor da aula
            const valorAulaInput = document.getElementById('valor_aula');
            if (valorAulaInput && typeof IMask !== 'undefined') {
                IMask(valorAulaInput, {
                    mask: Number,
                    scale: 2,
                    thousandsSeparator: '.',
                    radix: ',',
                    mapToRadix: ['.'],
                    min: 0,
                    max: 999999
                });
            }
        });
    </script>

    <!-- Modal Markup (moved into main content) -->
    <!-- Criar Reunião -->
    <div x-show="createMeeting" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="createMeeting = false"></div>
        <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-6 z-10">
            <h3 class="text-lg font-semibold mb-4">Nova Reunião</h3>
            <form @if(Route::has('meetings.store')) action="{{ route('meetings.store') }}" @else action="#" data-no-route="true" @endif onsubmit="event.preventDefault(); submitCreateMeeting(this);">
                @csrf
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Título</label>
                    <input name="titulo" type="text" required class="w-full rounded border-gray-300 p-2">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea name="descricao" class="w-full rounded border-gray-300 p-2"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="createMeeting = false" class="px-4 py-2 border rounded">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Criar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Criar Aula -->
    <div x-show="createAula" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="createAula = false"></div>
        <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-6 z-10">
            <h3 class="text-lg font-semibold mb-4">Nova Aula</h3>
            <form @if(Route::has('aulas.store')) action="{{ route('aulas.store') }}" @else action="#" data-no-route="true" @endif onsubmit="event.preventDefault(); submitCreateAula(this);">
                @csrf
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Aluno</label>
                    <select name="aluno_id" class="w-full rounded border-gray-300 p-2">
                        @foreach($alunos as $aluno)
                            <option value="{{ $aluno->id }}">{{ $aluno->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Data e Hora</label>
                    <input name="data_hora" type="datetime-local" required class="w-full rounded border-gray-300 p-2">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Duração (min)</label>
                    <input name="duracao_minutos" type="number" min="1" value="60" class="w-full rounded border-gray-300 p-2">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="createAula = false" class="px-4 py-2 border rounded">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Criar</button>
                </div>
            </form>
        </div>
    </div>

@endpush

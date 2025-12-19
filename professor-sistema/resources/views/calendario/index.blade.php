<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="calendarioApp()">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Calendário</h1>
                <p class="mt-1 text-sm text-gray-600">
                    @if($alunoSelecionado)
                        Filtrando aulas de: <strong>{{ $alunoSelecionado->nome }}</strong>
                        <a href="{{ route('calendario.index') }}" class="ml-2 text-blue-600 hover:text-blue-700 text-sm underline">
                            Limpar filtro
                        </a>
                    @else
                        Gerencie sua agenda de aulas e reuniões
                    @endif
                </p>
            </div>
            <x-button variant="primary" @click="showNovaReuniaoModal = true">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nova Reunião
            </x-button>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Controles do Calendário -->
        <x-card class="mb-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <!-- Navegação de Data -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('calendario.index', ['view' => $view, 'date' => $date->copy()->subWeek()->format('Y-m-d')]) }}" 
                       class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    
                    <div class="text-center">
                        <h2 class="text-xl font-bold text-gray-900">
                            @if($view === 'day')
                                {{ $date->format('d/m/Y') }}
                            @elseif($view === 'week')
                                Semana {{ $date->weekOfYear }} - {{ $date->format('M Y') }}
                            @else
                                {{ $date->format('F Y') }}
                            @endif
                        </h2>
                        <p class="text-sm text-gray-500">
                            {{ $startDate->format('d/m') }} a {{ $endDate->format('d/m/Y') }}
                        </p>
                    </div>
                    
                    <a href="{{ route('calendario.index', ['view' => $view, 'date' => $date->copy()->addWeek()->format('Y-m-d')]) }}" 
                       class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    
                    <a href="{{ route('calendario.index', ['view' => $view]) }}" 
                       class="ml-2 px-3 py-1 text-sm bg-blue-50 text-blue-600 rounded-md hover:bg-blue-100 transition-colors">
                        Hoje
                    </a>
                </div>

                <!-- Seletor de Visualização -->
                <div class="flex items-center gap-2 bg-gray-100 rounded-lg p-1">
                    <a href="{{ route('calendario.index', ['view' => 'day', 'date' => $date->format('Y-m-d')]) }}" 
                       class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ $view === 'day' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        Dia
                    </a>
                    <a href="{{ route('calendario.index', ['view' => 'week', 'date' => $date->format('Y-m-d')]) }}" 
                       class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ $view === 'week' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        Semana
                    </a>
                    <a href="{{ route('calendario.index', ['view' => 'month', 'date' => $date->format('Y-m-d')]) }}" 
                       class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ $view === 'month' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        Mês
                    </a>
                </div>
            </div>
        </x-card>

        <!-- Calendário Interativo -->
        <x-card>
            <div id="calendar"></div>
        </x-card>
    </div>

    <!-- Modal Nova Reunião -->
    <div x-data="{ showNovaReuniaoModal: false }" 
         x-cloak
         x-show="showNovaReuniaoModal"
         @keydown.escape.window="showNovaReuniaoModal = false"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showNovaReuniaoModal = false"></div>
            
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form method="POST" action="{{ route('calendario.storeReuniao') }}">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Nova Reunião</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Título *</label>
                                <input type="text" name="titulo" required maxlength="255"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Ex: Reunião com Pais, Avaliação...">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Aluno (opcional)</label>
                                <select name="aluno_id" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Reunião geral</option>
                                    @foreach($alunos as $aluno)
                                        <option value="{{ $aluno->id }}">{{ $aluno->nome }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Data e Hora *</label>
                                    <input type="datetime-local" name="data_hora" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Duração (min) *</label>
                                    <input type="number" name="duracao_minutos" required min="15" max="480" value="60"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
                                <textarea name="descricao" rows="3" maxlength="1000"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Detalhes sobre a reunião..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <x-button variant="primary" type="submit">
                            Agendar Reunião
                        </x-button>
                        <x-button variant="ghost" type="button" @click="showNovaReuniaoModal = false">
                            Cancelar
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/pt-br.global.min.js'></script>

    @push('scripts')
    <script>
        function calendarioApp() {
            return {
                showNovaReuniaoModal: false,
                calendar: null,
                
                init() {
                    this.initCalendar();
                },
                
                initCalendar() {
                    const calendarEl = document.getElementById('calendar');
                    
                    this.calendar = new FullCalendar.Calendar(calendarEl, {
                        locale: 'pt-br',
                        initialView: '{{ $view === "day" ? "timeGridDay" : ($view === "month" ? "dayGridMonth" : "timeGridWeek") }}',
                        initialDate: '{{ $date->format("Y-m-d") }}',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,timeGridDay'
                        },
                        buttonText: {
                            today: 'Hoje',
                            month: 'Mês',
                            week: 'Semana',
                            day: 'Dia'
                        },
                        slotMinTime: '06:00:00',
                        slotMaxTime: '23:00:00',
                        height: 'auto',
                        events: [
                            @foreach($aulas as $aula)
                            {
                                id: 'aula-{{ $aula->id }}',
                                title: '{{ $aula->aluno->nome }}',
                                start: '{{ $aula->data_hora->toIso8601String() }}',
                                end: '{{ $aula->data_hora->copy()->addMinutes($aula->duracao_minutos)->toIso8601String() }}',
                                backgroundColor: '{{ $aula->status === "realizada" ? "#10b981" : ($aula->status === "agendada" ? "#3b82f6" : "#f59e0b") }}',
                                borderColor: '{{ $aula->status === "realizada" ? "#059669" : ($aula->status === "agendada" ? "#2563eb" : "#d97706") }}',
                                extendedProps: {
                                    type: 'aula',
                                    aulaId: {{ $aula->id }},
                                    status: '{{ $aula->status }}',
                                    duracao: '{{ $aula->duracao_minutos }}min'
                                }
                            },
                            @endforeach
                            @foreach($reunioes as $reuniao)
                            {
                                id: 'reuniao-{{ $reuniao->id }}',
                                title: '🤝 {{ $reuniao->titulo }}',
                                start: '{{ $reuniao->data_hora->toIso8601String() }}',
                                end: '{{ $reuniao->data_hora->copy()->addMinutes($reuniao->duracao_minutos)->toIso8601String() }}',
                                backgroundColor: '{{ $reuniao->status === "realizada" ? "#8b5cf6" : ($reuniao->status === "agendada" ? "#6366f1" : "#6b7280") }}',
                                borderColor: '{{ $reuniao->status === "realizada" ? "#7c3aed" : ($reuniao->status === "agendada" ? "#4f46e5" : "#4b5563") }}',
                                extendedProps: {
                                    type: 'reuniao',
                                    reuniaoId: {{ $reuniao->id }},
                                    status: '{{ $reuniao->status }}',
                                    duracao: '{{ $reuniao->duracao_minutos }}min'
                                }
                            },
                            @endforeach
                        ],
                        eventClick: (info) => {
                            if (info.event.extendedProps.type === 'aula') {
                                window.location.href = `/aulas/${info.event.extendedProps.aulaId}`;
                            } else {
                                this.showReuniaoDetails(info.event.extendedProps.reuniaoId);
                            }
                        },
                        eventDidMount: (info) => {
                            // Adicionar tooltip
                            info.el.title = `${info.event.title}\n${info.event.extendedProps.duracao}`;
                        },
                        datesSet: (dateInfo) => {
                            // Atualizar URL quando mudar a visualização
                            const view = dateInfo.view.type === 'dayGridMonth' ? 'month' : (dateInfo.view.type === 'timeGridDay' ? 'day' : 'week');
                            const date = dateInfo.view.currentStart;
                            const dateStr = date.toISOString().split('T')[0];
                            
                            // Atualizar URL sem recarregar a página
                            const url = new URL(window.location);
                            url.searchParams.set('view', view);
                            url.searchParams.set('date', dateStr);
                            window.history.pushState({}, '', url);
                        }
                    });
                    
                    this.calendar.render();
                },
                
                showReuniaoDetails(reuniaoId) {
                    alert('Detalhes da reunião ' + reuniaoId + '\n\nEm breve: Modal com detalhes completos e opções de edição');
                }
            }
        }
    </script>
    @endpush
</x-app-layout>

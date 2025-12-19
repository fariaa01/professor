<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Alunos</h1>
                <p class="mt-1 text-sm text-gray-600">Gerencie seus alunos e acompanhe o progresso</p>
            </div>
            <x-button variant="primary" :href="route('alunos.create')">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Novo Aluno
            </x-button>
        </div>

        <!-- Filtros e Busca -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('alunos.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="md:col-span-2">
                    <x-input 
                        name="busca" 
                        placeholder="Buscar por nome, email ou telefone..." 
                        value="{{ request('busca') }}"
                        class="w-full"
                    />
                </div>
                
                <div>
                    <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos os Status</option>
                        <option value="ativo" {{ request('status') === 'ativo' ? 'selected' : '' }}>Ativo</option>
                        <option value="inativo" {{ request('status') === 'inativo' ? 'selected' : '' }}>Inativo</option>
                    </select>
                </div>

                <div>
                    <select name="tag" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todas as Tags</option>
                        @foreach($tags as $tag)
                            <option value="{{ $tag->id }}" {{ request('tag') == $tag->id ? 'selected' : '' }}>
                                {{ $tag->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex gap-2">
                    <x-button type="submit" variant="primary" class="flex-1">
                        Filtrar
                    </x-button>
                    @if(request()->hasAny(['busca', 'status', 'tag']))
                        <x-button variant="outline" :href="route('alunos.index')">
                            Limpar
                        </x-button>
                    @endif
                </div>
            </form>
        </x-card>

        <!-- Lista de Alunos -->
        @if($alunos->count() > 0)
            <div class="grid grid-cols-1 gap-4">
                @foreach($alunos as $aluno)
                    <x-card class="hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2 flex-wrap">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $aluno->nome }}</h3>
                                    <x-badge :variant="$aluno->ativo ? 'success' : 'secondary'">
                                        {{ $aluno->ativo ? 'Ativo' : 'Inativo' }}
                                    </x-badge>
                                    @foreach($aluno->tags as $tag)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                              style="background-color: {{ $tag->cor }}20; color: {{ $tag->cor }}; border: 1px solid {{ $tag->cor }}40;">
                                            {{ $tag->nome }}
                                        </span>
                                    @endforeach
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600 mb-3">
                                    @if($aluno->email)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ $aluno->email }}
                                        </div>
                                    @endif
                                    
                                    @if($aluno->telefone)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                            </svg>
                                            {{ $aluno->telefone }}
                                        </div>
                                    @endif
                                    
                                    @if($aluno->valor_aula)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            R$ {{ number_format($aluno->valor_aula, 2, ',', '.') }} /aula
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Indicadores Rápidos -->
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <!-- Total de Aulas -->
                                    <div class="text-center">
                                        <div class="flex items-center justify-center gap-1 text-gray-500 text-xs mb-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            Total
                                        </div>
                                        <div class="text-xl font-bold text-gray-900">{{ $aluno->aulas_count }}</div>
                                    </div>

                                    <!-- Aulas Realizadas -->
                                    <div class="text-center">
                                        <div class="flex items-center justify-center gap-1 text-gray-500 text-xs mb-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Realizadas
                                        </div>
                                        <div class="text-xl font-bold text-green-600">{{ $aluno->aulas_realizadas_count }}</div>
                                    </div>

                                    <!-- Faltas do Mês -->
                                    <div class="text-center">
                                        <div class="flex items-center justify-center gap-1 text-gray-500 text-xs mb-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Faltas/Mês
                                        </div>
                                        <div class="text-xl font-bold {{ $aluno->faltas_mes_count > 2 ? 'text-red-600' : 'text-gray-900' }}">
                                            {{ $aluno->faltas_mes_count }}
                                        </div>
                                    </div>

                                    <!-- Próxima Aula -->
                                    <div class="text-center">
                                        <div class="flex items-center justify-center gap-1 text-gray-500 text-xs mb-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Próxima
                                        </div>
                                        @if($aluno->proxima_aula)
                                            <div class="text-xs font-semibold text-blue-600">
                                                {{ \Carbon\Carbon::parse($aluno->proxima_aula->data_hora)->format('d/m') }}
                                            </div>
                                            <div class="text-xs text-gray-600">
                                                {{ \Carbon\Carbon::parse($aluno->proxima_aula->data_hora)->format('H:i') }}
                                            </div>
                                        @else
                                            <div class="text-xs text-gray-400">
                                                Sem aulas
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex flex-col gap-2 ml-4">
                                <!-- Linha 1: Ver Detalhes, Mensagens e Editar -->
                                <div class="flex gap-2">
                                    <x-button variant="outline" size="sm" :href="route('alunos.show', $aluno)">
                                        Ver Detalhes
                                    </x-button>
                                    <x-button variant="outline" size="sm" :href="route('mensagens.chat', $aluno)">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                        </svg>
                                        Mensagens
                                    </x-button>
                                    <x-button variant="ghost" size="sm" :href="route('alunos.edit', $aluno)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </x-button>
                                </div>
                                
                                <!-- Linha 2: Ações Rápidas -->
                                <div class="flex gap-2">
                                    <!-- Toggle Ativo/Inativo -->
                                    <form method="POST" action="{{ route('alunos.toggle-status', $aluno) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button 
                                            type="submit"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md transition-colors {{ $aluno->ativo ? 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }}"
                                            title="{{ $aluno->ativo ? 'Marcar como Inativo' : 'Marcar como Ativo' }}"
                                        >
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                @if($aluno->ativo)
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                @endif
                                            </svg>
                                            {{ $aluno->ativo ? 'Inativar' : 'Ativar' }}
                                        </button>
                                    </form>
                                    
                                    <!-- Ir para Calendário -->
                                    <a 
                                        href="{{ route('calendario.index', ['aluno' => $aluno->id]) }}"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors"
                                        title="Ver aulas no calendário"
                                    >
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Calendário
                                    </a>
                                </div>
                            </div>
                        </div>
                    </x-card>
                @endforeach
            </div>
            
            <!-- Paginação -->
            <div class="mt-6">
                {{ $alunos->links() }}
            </div>
        @else
            <x-card>
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum aluno cadastrado</h3>
                    <p class="text-gray-500 mb-4">Comece adicionando seu primeiro aluno</p>
                    <x-button variant="primary" :href="route('alunos.create')">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Cadastrar Primeiro Aluno
                    </x-button>
                </div>
            </x-card>
        @endif
    </div>
</x-app-layout>

<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header com Ações -->
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <x-button variant="ghost" :href="route('alunos.index')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </x-button>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $aluno->nome }}</h1>
                    <p class="mt-1 text-sm text-gray-600">Perfil completo e histórico</p>
                </div>
                <x-badge :variant="$aluno->ativo ? 'success' : 'secondary'">
                    {{ $aluno->ativo ? 'Ativo' : 'Inativo' }}
                </x-badge>
            </div>
            <x-button variant="outline" :href="route('alunos.edit', $aluno)">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Editar
            </x-button>
        </div>

        <!-- Informações Básicas -->
        <x-card class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informações de Contato</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                @if($aluno->email)
                    <div>
                        <span class="text-gray-500">E-mail</span>
                        <p class="font-medium text-gray-900">{{ $aluno->email }}</p>
                    </div>
                @endif
                @if($aluno->telefone)
                    <div>
                        <span class="text-gray-500">Telefone</span>
                        <p class="font-medium text-gray-900">{{ $aluno->telefone }}</p>
                    </div>
                @endif
                @if($aluno->endereco)
                    <div>
                        <span class="text-gray-500">Endereço</span>
                        <p class="font-medium text-gray-900">{{ $aluno->endereco }}</p>
                    </div>
                @endif
                @if($aluno->responsavel)
                    <div>
                        <span class="text-gray-500">Responsável</span>
                        <p class="font-medium text-gray-900">{{ $aluno->responsavel }}</p>
                    </div>
                @endif
                @if($aluno->telefone_responsavel)
                    <div>
                        <span class="text-gray-500">Tel. Responsável</span>
                        <p class="font-medium text-gray-900">{{ $aluno->telefone_responsavel }}</p>
                    </div>
                @endif
                @if($aluno->data_inicio)
                    <div>
                        <span class="text-gray-500">Início</span>
                        <p class="font-medium text-gray-900">{{ $aluno->data_inicio->format('d/m/Y') }}</p>
                    </div>
                @endif
            </div>
        </x-card>

        <!-- Horários das Aulas -->
        @if($aluno->horariosAtivos->count() > 0)
        <x-card class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Horários Semanais</h3>
                <x-button variant="ghost" :href="route('alunos.edit', $aluno)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </x-button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($aluno->horariosAtivos->sortBy('dia_semana') as $horario)
                    <div class="p-4 bg-blue-50 border border-blue-100 rounded-lg">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="font-semibold text-gray-900">{{ $horario->dia_semana_texto }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ substr($horario->hora_inicio, 0, 5) }} às {{ substr($horario->hora_fim, 0, 5) }}</span>
                        </div>
                        <div class="mt-1 text-xs text-gray-500">
                            {{ $horario->duracao_minutos }} minutos
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>
        @endif

        <!-- Estatísticas Gerais -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <x-stat-card title="Total de Aulas" :value="$totalAulas" />
            <x-stat-card title="Aulas Realizadas" :value="$aulasRealizadas" />
            <x-stat-card title="Faltas" :value="$faltasAluno" />
            <x-stat-card title="Frequência" :value="$taxaFrequencia . '%'" />
        </div>

        <!-- Estatísticas Financeiras -->
        <x-card class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Controle Financeiro</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Valor da Aula</p>
                    <p class="text-2xl font-bold text-gray-900">R$ {{ number_format($aluno->valor_aula ?? 0, 2, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Recebido</p>
                    <p class="text-2xl font-bold text-green-600">R$ {{ number_format($totalRecebido, 2, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Valor Pendente</p>
                    <p class="text-2xl font-bold text-yellow-600">R$ {{ number_format($valorPendente, 2, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Valor Atrasado</p>
                    <p class="text-2xl font-bold text-red-600">R$ {{ number_format($valorAtrasado, 2, ',', '.') }}</p>
                </div>
            </div>
        </x-card>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Histórico de Aulas -->
            <div class="lg:col-span-2">
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Histórico de Aulas</h3>
                    
                    @if($historicoAulas->count() > 0)
                        <div class="space-y-3">
                            @foreach($historicoAulas as $aula)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border-l-4 
                                    @if($aula->status === 'realizada') border-green-500
                                    @elseif($aula->status === 'agendada') border-blue-500
                                    @else border-red-500
                                    @endif
                                ">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-1">
                                            <p class="font-medium text-gray-900">{{ $aula->data_hora->format('d/m/Y H:i') }}</p>
                                            <x-badge 
                                                :variant="$aula->status === 'realizada' ? 'success' : ($aula->status === 'agendada' ? 'info' : 'danger')"
                                            >
                                                {{ ucfirst(str_replace('_', ' ', $aula->status)) }}
                                            </x-badge>
                                            @if($aula->status_pagamento)
                                                <x-badge 
                                                    :variant="$aula->status_pagamento === 'pago' ? 'success' : ($aula->status_pagamento === 'pendente' ? 'warning' : 'danger')"
                                                >
                                                    {{ ucfirst($aula->status_pagamento) }}
                                                </x-badge>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600">{{ $aula->duracao_minutos }}min</p>
                                        @if($aula->conteudo)
                                            <p class="text-sm text-gray-700 mt-1">{{ $aula->conteudo }}</p>
                                        @endif
                                        @if($aula->valor)
                                            <p class="text-sm font-medium text-green-600 mt-1">R$ {{ number_format($aula->valor, 2, ',', '.') }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $historicoAulas->links() }}
                        </div>
                    @else
                        <p class="text-center text-gray-500 py-8">Nenhuma aula registrada ainda</p>
                    @endif
                </x-card>
            </div>

            <!-- Próximas Aulas -->
            <div class="lg:col-span-1">
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Próximas Aulas</h3>
                    
                    @if($proximasAulas->count() > 0)
                        <div class="space-y-3">
                            @foreach($proximasAulas as $aula)
                                <div class="p-3 bg-blue-50 rounded-lg">
                                    <p class="font-medium text-gray-900">{{ $aula->data_hora->format('d/m/Y') }}</p>
                                    <p class="text-sm text-gray-600">{{ $aula->data_hora->format('H:i') }} ({{ $aula->duracao_minutos }}min)</p>
                                    @if($aula->valor)
                                        <p class="text-sm font-medium text-green-600 mt-1">R$ {{ number_format($aula->valor, 2, ',', '.') }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-gray-500 py-8 text-sm">Nenhuma aula agendada</p>
                    @endif
                </x-card>

                <!-- Observações -->
                @if($aluno->observacoes)
                    <x-card class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Observações</h3>
                        <p class="text-sm text-gray-600">{{ $aluno->observacoes }}</p>
                    </x-card>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

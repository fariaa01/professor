<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Relatórios</h1>
            <p class="mt-1 text-sm text-gray-600">Análise detalhada e controle financeiro</p>
        </div>

        <!-- Filtros -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('relatorios.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data Início</label>
                    <input 
                        type="date" 
                        name="data_inicio" 
                        value="{{ $dataInicio->format('Y-m-d') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data Fim</label>
                    <input 
                        type="date" 
                        name="data_fim" 
                        value="{{ $dataFim->format('Y-m-d') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Aluno (opcional)</label>
                    <select 
                        name="aluno_id"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos os alunos</option>
                        @foreach($alunos as $aluno)
                            <option value="{{ $aluno->id }}" {{ $alunoId == $aluno->id ? 'selected' : '' }}>
                                {{ $aluno->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <x-button variant="primary" type="submit" class="w-full">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Filtrar
                    </x-button>
                </div>
            </form>
        </x-card>

        <!-- Período Selecionado -->
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-800">
                <strong>Período:</strong> {{ $dataInicio->format('d/m/Y') }} até {{ $dataFim->format('d/m/Y') }}
                @if($alunoId)
                    <span class="ml-4"><strong>Aluno:</strong> {{ $alunos->find($alunoId)->nome }}</span>
                @endif
            </p>
        </div>

        <!-- Estatísticas Gerais -->
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Resumo Geral</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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
                    title="Faltas (Alunos)" 
                    :value="$faltasAluno"
                    :icon="'<svg class=\'w-6 h-6 text-yellow-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z\'></path></svg>'"
                />
                
                <x-stat-card 
                    title="Carga Horária" 
                    :value="number_format($cargaHoraria / 60, 1) . 'h'"
                    :icon="'<svg class=\'w-6 h-6 text-purple-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
                />
            </div>
        </div>

        <!-- Estatísticas Financeiras -->
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Visão Financeira</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-stat-card 
                    title="Faturamento Total" 
                    :value="'R$ ' . number_format($faturamentoTotal, 2, ',', '.')"
                    :icon="'<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
                />
                
                <x-stat-card 
                    title="Valor Recebido" 
                    :value="'R$ ' . number_format($valorRecebido, 2, ',', '.')"
                    :icon="'<svg class=\'w-6 h-6 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
                />
                
                <x-stat-card 
                    title="Valor Pendente" 
                    :value="'R$ ' . number_format($valorPendente, 2, ',', '.')"
                    :icon="'<svg class=\'w-6 h-6 text-yellow-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
                />
                
                <x-stat-card 
                    title="Valor Atrasado" 
                    :value="'R$ ' . number_format($valorAtrasado, 2, ',', '.')"
                    :icon="'<svg class=\'w-6 h-6 text-red-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z\'></path></svg>'"
                />
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Ranking por Aluno -->
            <x-card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Análise por Aluno</h3>
                
                @if($aulasPorAluno->count() > 0)
                    <div class="space-y-4">
                        @foreach($aulasPorAluno as $item)
                            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                                <!-- Header: Nome + Status + Plano -->
                                <div class="px-4 py-3 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <h4 class="text-lg font-bold text-gray-900">{{ $item['aluno']->nome }}</h4>
                                            @if($item['plano'])
                                                <x-badge variant="info" size="sm">{{ $item['plano']->tipo_plano_nome }}</x-badge>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2">
                                            @php
                                                $statusPagamento = $item['pendente'] == 0 ? 'success' : ($item['proximas_parcelas']->where('status_pagamento', 'atrasado')->count() > 0 ? 'danger' : 'warning');
                                            @endphp
                                            <x-badge :variant="$statusPagamento">
                                                @if($statusPagamento === 'success') Em dia
                                                @elseif($statusPagamento === 'danger') Atrasado
                                                @else Pendente
                                                @endif
                                            </x-badge>
                                        </div>
                                    </div>
                                </div>

                                <!-- Body: Indicadores Financeiros -->
                                <div class="px-4 py-3">
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-green-50 rounded-lg border border-green-200">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-xs font-medium text-green-700">Recebido:</span>
                                            <span class="text-sm font-bold text-green-900">R$ {{ number_format($item['recebido'], 2, ',', '.') }}</span>
                                        </div>

                                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-yellow-50 rounded-lg border border-yellow-200">
                                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-xs font-medium text-yellow-700">Pendente:</span>
                                            <span class="text-sm font-bold text-yellow-900">R$ {{ number_format($item['pendente'], 2, ',', '.') }}</span>
                                        </div>

                                        @if($item['plano'])
                                            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 rounded-lg border border-blue-200">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <span class="text-xs font-medium text-blue-700">Total Plano:</span>
                                                <span class="text-sm font-bold text-blue-900">
                                                    @if($item['plano']->tipo_plano === 'por_aula')
                                                        R$ {{ number_format($item['plano']->valor_aula, 2, ',', '.') }}/aula
                                                    @else
                                                        R$ {{ number_format($item['plano']->valor_total, 2, ',', '.') }}
                                                    @endif
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Próximas Cobranças (Compacto) -->
                                    @if($item['proximas_parcelas']->count() > 0)
                                        <div class="mt-3 pt-3 border-t border-gray-100">
                                            <p class="text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wide">Próximas Cobranças</p>
                                            <div class="space-y-1.5">
                                                @foreach($item['proximas_parcelas']->take(3) as $parcela)
                                                    <div class="flex items-center justify-between text-xs py-1.5 px-2 rounded
                                                        @if($parcela->status_pagamento === 'atrasado') bg-red-50 border border-red-200
                                                        @else bg-gray-50 border border-gray-100
                                                        @endif
                                                    ">
                                                        <div class="flex items-center gap-2">
                                                            <x-badge :variant="$parcela->status_variant" size="sm">{{ $parcela->parcela_formatada }}</x-badge>
                                                            <span class="text-gray-700">{{ $parcela->data_vencimento->format('d/m/Y') }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-bold text-gray-900">R$ {{ number_format($parcela->valor, 2, ',', '.') }}</span>
                                                            <span class="text-xs font-medium
                                                                @if($parcela->status_pagamento === 'atrasado') text-red-700
                                                                @elseif($parcela->status_pagamento === 'pago') text-green-700
                                                                @else text-yellow-700
                                                                @endif
                                                            ">
                                                                {{ ucfirst($parcela->status_pagamento) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @if($item['proximas_parcelas']->count() > 3)
                                                <p class="text-xs text-gray-500 mt-2 text-center">+ {{ $item['proximas_parcelas']->count() - 3 }} parcelas futuras</p>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Detalhes do Plano (Discreto) -->
                                    @if($item['plano'])
                                        <div class="mt-3 pt-3 border-t border-gray-100">
                                            <details class="group">
                                                <summary class="cursor-pointer text-xs font-medium text-gray-600 hover:text-gray-900 flex items-center gap-1">
                                                    <svg class="w-3 h-3 transform group-open:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                    Detalhes do Plano
                                                </summary>
                                                <div class="mt-2 pl-4 text-xs text-gray-600 space-y-1">
                                                    @if($item['plano']->tipo_plano === 'pacote')
                                                        <p><span class="font-medium">Quantidade:</span> {{ $item['plano']->quantidade_aulas }} aulas</p>
                                                        <p><span class="font-medium">Valor/aula:</span> R$ {{ number_format($item['plano']->valor_total / $item['plano']->quantidade_aulas, 2, ',', '.') }}</p>
                                                    @endif
                                                    <p><span class="font-medium">Período:</span> {{ $item['plano']->data_inicio->format('d/m/Y') }} até {{ $item['plano']->data_fim ? $item['plano']->data_fim->format('d/m/Y') : 'indeterminado' }}</p>
                                                    <p><span class="font-medium">Aulas realizadas:</span> {{ $item['realizadas'] }} · <span class="font-medium">Faltas:</span> {{ $item['faltas'] }}</p>
                                                </div>
                                            </details>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500 py-8">Nenhum dado encontrado para o período selecionado</p>
                @endif
            </x-card>

            <!-- Resumo Rápido -->
            <x-card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Indicadores</h3>
                
                <div class="space-y-4">
                    <!-- Taxa de Realização -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Taxa de Realização</span>
                            <span class="text-sm font-semibold text-gray-900">
                                {{ $totalAulas > 0 ? number_format(($aulasRealizadas / $totalAulas) * 100, 1) : 0 }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $totalAulas > 0 ? ($aulasRealizadas / $totalAulas) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Taxa de Pagamento -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Taxa de Pagamento</span>
                            <span class="text-sm font-semibold text-gray-900">
                                {{ $faturamentoTotal > 0 ? number_format(($valorRecebido / $faturamentoTotal) * 100, 1) : 0 }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $faturamentoTotal > 0 ? ($valorRecebido / $faturamentoTotal) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Média de Horas por Dia -->
                    <div class="p-4 bg-purple-50 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Média de Horas por Dia</p>
                        <p class="text-2xl font-bold text-purple-600">
                            {{ $totalAulas > 0 ? number_format($cargaHoraria / 60 / $dataInicio->diffInDays($dataFim, true), 1) : 0 }}h
                        </p>
                    </div>

                    <!-- Ticket Médio -->
                    <div class="p-4 bg-green-50 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Ticket Médio por Aula</p>
                        <p class="text-2xl font-bold text-green-600">
                            R$ {{ $aulasRealizadas > 0 ? number_format($faturamentoTotal / $aulasRealizadas, 2, ',', '.') : '0,00' }}
                        </p>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Histórico Detalhado -->
        <x-card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Histórico Detalhado</h3>
                <span class="text-sm text-gray-500">{{ $historicoAulas->total() }} aulas</span>
            </div>

            @if($historicoAulas->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data/Hora</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aluno</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duração</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pagamento</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($historicoAulas as $aula)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        <div class="font-medium text-gray-900">{{ $aula->data_hora->format('d/m/Y') }}</div>
                                        <div class="text-gray-500">{{ $aula->data_hora->format('H:i') }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $aula->aluno->nome }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $aula->duracao_minutos }}min
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        <x-badge :variant="$aula->status === 'realizada' ? 'success' : ($aula->status === 'agendada' ? 'info' : 'warning')">
                                            @if($aula->status === 'cancelada_aluno') Falta
                                            @else {{ ucfirst($aula->status) }}
                                            @endif
                                        </x-badge>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        @if($aula->status_pagamento)
                                            <x-badge :variant="$aula->status_pagamento === 'pago' ? 'success' : ($aula->status_pagamento === 'atrasado' ? 'danger' : 'warning')">
                                                {{ ucfirst($aula->status_pagamento) }}
                                            </x-badge>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        @if($aula->valor)
                                            R$ {{ number_format($aula->valor, 2, ',', '.') }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 max-w-xs truncate">
                                        {{ $aula->conteudo ?? $aula->observacoes ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="mt-4">
                    {{ $historicoAulas->links() }}
                </div>
            @else
                <p class="text-center text-gray-500 py-8">Nenhuma aula encontrada para o período selecionado</p>
            @endif
        </x-card>
    </div>
</x-app-layout>

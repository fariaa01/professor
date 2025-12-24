<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dashboard Aluno</title>
    <style>
        .modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;}
        .modal{background:#fff;padding:20px;border-radius:8px;max-width:420px;width:100%;box-shadow:0 8px 24px rgba(0,0,0,0.2);} 
        .modal h2{margin-top:0}
        .hidden{display:none}
        .error{color:#c00}
        .success{color:#070}
        .modal .actions{display:flex;gap:8px;justify-content:flex-end;margin-top:12px}
        .modal input[type="text"]{width:100%;padding:8px;border:1px solid #ccc;border-radius:4px}
        .modal button{padding:8px 12px;border-radius:4px;border:0;background:#2563eb;color:#fff}
        .modal .btn-secondary{background:#6b7280}
    </style>
</head>
<body>
    <h1>Dashboard do Aluno</h1>

    @if(session('status'))
        <div class="success">{{ session('status') }}</div>
    @endif

    @if(! $connected)
        <p>Você ainda não está conectado a nenhum professor.</p>
        <p>Por favor, informe o ID do professor para se conectar.</p>
        <button id="open-connect">Conectar com um professor</button>

        <!-- Modal de conexão -->
        <div id="connect-modal" class="modal-backdrop hidden" role="dialog" aria-modal="true">
            <div class="modal">
                <h2>Conectar com Professor</h2>
                @if($errors->has('professor_id'))
                    <div class="error">{{ $errors->first('professor_id') }}</div>
                @endif
                <form method="POST" action="{{ route('aluno.connect.post') }}">
                    @csrf
                    <label for="professor_id">ID do professor</label>
                    <input id="professor_id" name="professor_id" type="text" value="{{ old('professor_id') }}" required />
                    <div class="actions">
                        <button type="button" class="btn-secondary" id="close-connect">Fechar</button>
                        <button type="submit">Conectar</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            const modal = document.getElementById('connect-modal');
            const openBtn = document.getElementById('open-connect');
            const closeBtn = document.getElementById('close-connect');
            openBtn.addEventListener('click', ()=> modal.classList.remove('hidden'));
            closeBtn.addEventListener('click', ()=> modal.classList.add('hidden'));
            // se há erros de validação ou não conectado, abrir automaticamente
            @if($errors->any())
                modal.classList.remove('hidden');
            @elseif(! $connected)
                // abrir automaticamente na primeira visita
                modal.classList.remove('hidden');
            @endif
        </script>

    @else
        <!-- Aluno conectado: conteúdo do dashboard será carregado abaixo -->
    @endif

</body>
</html>
<x-aluno-layout>
    <div class="px-4 sm:px-6 lg:px-8" style="max-width: 1400px; margin: 0 auto;">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Meu Painel</h1>
                <p class="mt-1 text-sm text-gray-600">Bem-vindo(a), <span id="alunoNome"></span></p>
            </div>
            <button onclick="logout()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Sair
            </button>
        </div>

        <!-- Estatísticas -->
        <div id="estatisticas" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6"></div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Próximas Aulas -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Próximas Aulas</h3>
                <div id="proximasAulas" class="space-y-3">
                    <p class="text-center text-gray-500 py-8">Carregando...</p>
                </div>
            </div>

            <!-- Aulas Recentes -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Aulas Recentes</h3>
                <div id="aulasRecentes" class="space-y-3">
                    <p class="text-center text-gray-500 py-8">Carregando...</p>
                </div>
            </div>
        </div>

        <!-- Plano Contratado -->
        <div id="planoContainer" class="mt-6"></div>
    </div>

    <script>
        const API_DADOS_URL = '/aluno/dashboard/dados';

        async function loadDashboard() {
            try {
                const response = await fetch(API_DADOS_URL, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin'
                });

                if (response.status === 401) {
                    // aluno não autenticado - redirecionar para login seguro
                    window.location.href = '{{ route("aluno.login") }}';
                    return;
                }

                const data = await response.json();

                if (data.success) {
                    renderEstatisticas(data.data.estatisticas);
                    renderProximasAulas(data.data.proximas_aulas);
                    renderAulasRecentes(data.data.aulas_recentes);
                    renderPlano(data.data.plano);
                } else {
                    console.error('Falha ao carregar dados do dashboard:', data.message || data);
                }
            } catch (error) {
                console.error('Erro ao carregar dashboard:', error);
            }
        }

        function renderEstatisticas(stats) {
            const cards = [
                { title: 'Total de Aulas', value: stats.total_aulas, color: 'blue' },
                { title: 'Aulas Realizadas', value: stats.aulas_realizadas, color: 'green' },
                { title: 'Faltas', value: stats.faltas, color: 'yellow' },
                { title: 'Carga Horária', value: stats.carga_horaria_horas + 'h', color: 'purple' }
            ];

            document.getElementById('estatisticas').innerHTML = cards.map(card => `
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-sm text-gray-600 mb-1">${card.title}</p>
                    <p class="text-3xl font-bold text-${card.color}-600">${card.value}</p>
                </div>
            `).join('');
        }

        function renderProximasAulas(aulas) {
            const container = document.getElementById('proximasAulas');
            if (aulas.length === 0) {
                container.innerHTML = '<p class="text-center text-gray-500 py-8">Nenhuma aula agendada</p>';
                return;
            }

            container.innerHTML = aulas.map(aula => `
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-gray-900">${aula.data_hora}</p>
                            <p class="text-sm text-gray-600">${aula.duracao_minutos} minutos</p>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Agendada</span>
                    </div>
                </div>
            `).join('');
        }

        function renderAulasRecentes(aulas) {
            const container = document.getElementById('aulasRecentes');
            if (aulas.length === 0) {
                container.innerHTML = '<p class="text-center text-gray-500 py-8">Nenhuma aula realizada ainda</p>';
                return;
            }

            container.innerHTML = aulas.map(aula => `
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="font-semibold text-gray-900 mb-1">${aula.data_hora}</p>
                    ${aula.conteudo ? `<p class="text-sm text-gray-600">${aula.conteudo}</p>` : ''}
                    <div class="flex gap-2 mt-2">
                        ${aula.tem_materiais ? '<span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded">Materiais</span>' : ''}
                        ${aula.tem_exercicios ? '<span class="text-xs px-2 py-1 bg-purple-100 text-purple-800 rounded">Exercícios</span>' : ''}
                    </div>
                </div>
            `).join('');
        }

        function renderPlano(plano) {
            const container = document.getElementById('planoContainer');
            if (!plano) return;

            container.innerHTML = `
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Plano Contratado</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <div class="p-4 bg-indigo-50 rounded-lg">
                            <p class="text-sm text-indigo-600 mb-1">Tipo de Plano</p>
                            <p class="font-semibold text-indigo-900">${plano.tipo_plano_nome}</p>
                        </div>
                        ${plano.valor_total ? `
                            <div class="p-4 bg-green-50 rounded-lg">
                                <p class="text-sm text-green-600 mb-1">Valor Total</p>
                                <p class="font-semibold text-green-900">R$ ${parseFloat(plano.valor_total).toFixed(2)}</p>
                            </div>
                        ` : ''}
                        ${plano.quantidade_aulas ? `
                            <div class="p-4 bg-blue-50 rounded-lg">
                                <p class="text-sm text-blue-600 mb-1">Quantidade de Aulas</p>
                                <p class="font-semibold text-blue-900">${plano.quantidade_aulas} aulas</p>
                            </div>
                        ` : ''}
                        <div class="p-4 bg-purple-50 rounded-lg">
                            <p class="text-sm text-purple-600 mb-1">Período</p>
                            <p class="font-semibold text-purple-900">${plano.data_inicio} até ${plano.data_fim || 'indeterminado'}</p>
                        </div>
                    </div>

                    ${plano.proximas_parcelas && plano.proximas_parcelas.length > 0 ? `
                        <div class="pt-4 border-t border-gray-200">
                            <h4 class="font-semibold text-gray-900 mb-3">Próximas Parcelas</h4>
                            <div class="space-y-2">
                                ${plano.proximas_parcelas.map(parcela => `
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded border ${parcela.status_pagamento === 'atrasado' ? 'border-red-300 bg-red-50' : 'border-gray-200'}">
                                        <div class="flex items-center gap-3">
                                            <span class="px-2 py-1 rounded text-xs font-medium ${parcela.status_pagamento === 'atrasado' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'}">
                                                ${parcela.numero_parcela}/${parcela.total_parcelas}
                                            </span>
                                            <span class="text-sm font-medium text-gray-900">${parcela.data_vencimento}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-gray-900">R$ ${parseFloat(parcela.valor).toFixed(2)}</span>
                                            <span class="px-2 py-1 rounded text-xs font-medium ${parcela.status_pagamento === 'pago' ? 'bg-green-100 text-green-800' : (parcela.status_pagamento === 'atrasado' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')}">
                                                ${parcela.status_pagamento.charAt(0).toUpperCase() + parcela.status_pagamento.slice(1)}
                                            </span>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;
        }

        function logout() {
            localStorage.removeItem('aluno_token');
            localStorage.removeItem('aluno_data');
            window.location.href = '{{ route("aluno.login") }}';
        }

        loadDashboard();
    </script>
</x-aluno-layout>

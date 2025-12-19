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
        const API_BASE_URL = 'http://localhost:8000/api/aluno';
        const token = localStorage.getItem('aluno_token');
        const alunoData = JSON.parse(localStorage.getItem('aluno_data') || '{}');

        if (!token) {
            window.location.href = '{{ route("aluno.login") }}';
        }

        document.getElementById('alunoNome').textContent = alunoData.nome || '';

        async function loadDashboard() {
            try {
                const response = await fetch(`${API_BASE_URL}/dashboard`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                const data = await response.json();

                if (data.success) {
                    renderEstatisticas(data.data.estatisticas);
                    renderProximasAulas(data.data.proximas_aulas);
                    renderAulasRecentes(data.data.aulas_recentes);
                    renderPlano(data.data.plano);
                } else {
                    alert('Sessão expirada. Faça login novamente.');
                    logout();
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

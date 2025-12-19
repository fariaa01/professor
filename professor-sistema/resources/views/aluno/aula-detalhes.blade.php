<x-aluno-layout>
    <div class="px-4 sm:px-6 lg:px-8" style="max-width: 1400px; margin: 0 auto;">
        <!-- Breadcrumb -->
        <nav class="mb-6">
            <ol class="flex items-center space-x-2 text-sm text-gray-500">
                <li><a href="{{ route('aluno.dashboard') }}" class="hover:text-gray-700">Painel</a></li>
                <li><span>/</span></li>
                <li><a href="/aluno/aulas" class="hover:text-gray-700">Minhas Aulas</a></li>
                <li><span>/</span></li>
                <li class="text-gray-900 font-medium" id="breadcrumbAula">Detalhes</li>
            </ol>
        </nav>

        <!-- Carregando -->
        <div id="loading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            <p class="mt-2 text-gray-500">Carregando detalhes da aula...</p>
        </div>

        <div id="aulaDetalhes" class="hidden">
            <div class="bg-gradient-to-r from-blue-600 to-cyan-500 rounded-lg shadow-lg p-8 mb-6 text-white">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold mb-3" id="aulaDataHora"></h1>
                        <div class="inline-flex items-center bg-white bg-opacity-20 backdrop-blur-sm rounded-lg px-4 py-2 mb-4">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium" id="aulaDuracao"></span>
                        </div>
                        <div id="aulaTags" class="flex flex-wrap gap-2"></div>
                    </div>
                    <span id="aulaStatus" class="px-5 py-2.5 rounded-full text-sm font-semibold bg-white shadow-md"></span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Coluna Principal -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Conteúdo da Aula -->
                    <div class="bg-white rounded-lg shadow p-6" id="conteudoSection">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Conteúdo da Aula
                        </h2>
                        <div id="aulaConteudo" class="text-gray-700 whitespace-pre-wrap"></div>
                    </div>

                    <!-- Materiais -->
                    <div class="bg-white rounded-lg shadow p-6" id="materiaisSection" style="display: none;">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            Materiais de Estudo
                        </h2>
                        <div id="aulaMateriais" class="space-y-2"></div>
                    </div>

                    <!-- Exercícios -->
                    <div class="bg-white rounded-lg shadow p-6" id="exerciciosSection" style="display: none;">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            Exercícios e Tarefas
                        </h2>
                        <div id="aulaExercicios" class="text-gray-700 whitespace-pre-wrap"></div>
                    </div>
                </div>

                <!-- Coluna Lateral -->
                <div class="space-y-6">
                    <!-- Observações do Professor -->
                    <div class="bg-white rounded-lg shadow p-6" id="observacoesSection" style="display: none;">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                            Observações do Professor
                        </h3>
                        <div id="aulaObservacoes" class="text-sm text-gray-700 whitespace-pre-wrap"></div>
                    </div>

                    <!-- Dificuldades -->
                    <div class="bg-white rounded-lg shadow p-6" id="dificuldadesSection" style="display: none;">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            Dificuldades Identificadas
                        </h3>
                        <div id="aulaDificuldades" class="text-sm text-gray-700 whitespace-pre-wrap"></div>
                    </div>

                    <!-- Pontos de Atenção -->
                    <div class="bg-white rounded-lg shadow p-6" id="pontosAtencaoSection" style="display: none;">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Pontos de Atenção
                        </h3>
                        <div id="aulaPontosAtencao" class="text-sm text-gray-700 whitespace-pre-wrap"></div>
                    </div>
                </div>
            </div>

            <!-- Botão Voltar -->
            <div class="mt-6">
                <a href="/aluno/aulas" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Voltar para Minhas Aulas
                </a>
            </div>
        </div>
    </div>

    <script>
        const API_BASE_URL = 'http://localhost:8000/api/aluno';
        const token = localStorage.getItem('aluno_token');
        const aulaId = window.location.pathname.split('/').pop();

        if (!token) {
            window.location.href = '{{ route("aluno.login") }}';
        }

        async function carregarDetalhes() {
            try {
                const response = await fetch(`${API_BASE_URL}/aulas/${aulaId}`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                const data = await response.json();

                if (data.success) {
                    renderizarAula(data.data);
                } else {
                    alert('Aula não encontrada.');
                    window.location.href = '/aluno/aulas';
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao carregar detalhes da aula.');
            }
        }

        function renderizarAula(aula) {
            // Header
            document.getElementById('aulaDataHora').textContent = aula.data_hora_completa || aula.data_hora;
            document.getElementById('aulaDuracao').textContent = aula.duracao_formatada || `${aula.duracao_minutos} minutos`;
            document.getElementById('breadcrumbAula').textContent = aula.data_hora;

            // Status
            const statusEl = document.getElementById('aulaStatus');
            statusEl.textContent = aula.status_nome;
            statusEl.className = `px-4 py-2 rounded-full text-sm font-semibold ${getStatusColorText(aula.status)}`;

            // Tags
            if (aula.tags && aula.tags.length > 0) {
                document.getElementById('aulaTags').innerHTML = aula.tags.map(tag => `
                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-white bg-opacity-25 backdrop-blur-sm">
                        ${tag.nome}
                    </span>
                `).join('');
            }

            // Conteúdo
            if (aula.conteudo) {
                document.getElementById('aulaConteudo').textContent = aula.conteudo;
            } else if (aula.e_futura) {
                document.getElementById('aulaConteudo').innerHTML = '<p class="text-gray-500 italic">O conteúdo desta aula será disponibilizado após sua realização.</p>';
            } else {
                document.getElementById('aulaConteudo').innerHTML = '<p class="text-gray-500 italic">Nenhum conteúdo registrado para esta aula.</p>';
            }

            // Materiais
            if (aula.materiais) {
                document.getElementById('materiaisSection').style.display = 'block';
                document.getElementById('aulaMateriais').innerHTML = aula.materiais;
            }

            // Exercícios
            if (aula.exercicios) {
                document.getElementById('exerciciosSection').style.display = 'block';
                document.getElementById('aulaExercicios').textContent = aula.exercicios;
            }

            // Observações
            if (aula.observacoes) {
                document.getElementById('observacoesSection').style.display = 'block';
                document.getElementById('aulaObservacoes').textContent = aula.observacoes;
            }

            // Dificuldades
            if (aula.dificuldades) {
                document.getElementById('dificuldadesSection').style.display = 'block';
                document.getElementById('aulaDificuldades').textContent = aula.dificuldades;
            }

            // Pontos de Atenção
            if (aula.pontos_atencao) {
                document.getElementById('pontosAtencaoSection').style.display = 'block';
                document.getElementById('aulaPontosAtencao').textContent = aula.pontos_atencao;
            }

            // Mostrar conteúdo e esconder loading
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('aulaDetalhes').classList.remove('hidden');
        }

        function getStatusColorText(status) {
            const cores = {
                'realizada': 'bg-green-500 text-white',
                'pendente': 'bg-blue-500 text-white',
                'reagendada': 'bg-yellow-500 text-white',
                'cancelada': 'bg-red-500 text-white',
                'falta': 'bg-gray-500 text-white',
            };
            return cores[status] || 'bg-gray-500 text-white';
        }

        carregarDetalhes();
    </script>
</x-aluno-layout>
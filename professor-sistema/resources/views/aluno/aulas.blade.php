<x-aluno-layout>
    <div class="px-4 sm:px-6 lg:px-8" style="max-width: 1400px; margin: 0 auto;">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Minhas Aulas</h1>
            <p class="mt-1 text-sm text-gray-600">Acompanhe todo o seu histórico de aulas e conteúdos estudados</p>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6 border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Período</label>
                    <select id="filtroPeriodo" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Todas as aulas</option>
                        <option value="futuras">Próximas aulas</option>
                        <option value="passadas">Aulas concluídas</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="filtroStatus" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Todos</option>
                        <option value="pendente">Agendada</option>
                        <option value="realizada">Realizada</option>
                        <option value="falta">Falta</option>
                        <option value="reagendada">Reagendada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tag/Categoria</label>
                    <select id="filtroTag" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Todas as tags</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ordenar por</label>
                    <select id="filtroOrdem" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="desc">Mais recentes</option>
                        <option value="asc">Mais antigas</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Lista de Aulas -->
        <div id="listaAulas" class="space-y-4">
            <div class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                <p class="mt-2 text-gray-500">Carregando aulas...</p>
            </div>
        </div>

        <!-- Paginação -->
        <div id="paginacao" class="mt-6"></div>
    </div>

    <script>
        const API_BASE_URL = 'http://localhost:8000/api/aluno';
        const token = localStorage.getItem('aluno_token');
        let paginaAtual = 1;

        if (!token) {
            window.location.href = '{{ route("aluno.login") }}';
        }

        // Event listeners para filtros
        document.getElementById('filtroPeriodo').addEventListener('change', () => {
            paginaAtual = 1;
            carregarAulas();
        });
        document.getElementById('filtroStatus').addEventListener('change', () => {
            paginaAtual = 1;
            carregarAulas();
        });
        document.getElementById('filtroTag').addEventListener('change', () => {
            paginaAtual = 1;
            carregarAulas();
        });
        document.getElementById('filtroOrdem').addEventListener('change', () => {
            paginaAtual = 1;
            carregarAulas();
        });

        async function carregarAulas(pagina = 1) {
            try {
                const periodo = document.getElementById('filtroPeriodo').value;
                const status = document.getElementById('filtroStatus').value;
                const tag = document.getElementById('filtroTag').value;
                const ordem = document.getElementById('filtroOrdem').value;

                let url = `${API_BASE_URL}/aulas?page=${pagina}&ordem=${ordem}`;
                if (periodo) url += `&periodo=${periodo}`;
                if (status) url += `&status=${status}`;
                if (tag) url += `&tag_id=${tag}`;

                console.log('Carregando aulas:', url);
                console.log('Token:', token ? 'presente' : 'ausente');

                const response = await fetch(url, {
                    headers: { 
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                console.log('Status da resposta:', response.status);

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Erro na resposta:', errorText);
                    throw new Error(`HTTP ${response.status}: ${errorText.substring(0, 100)}`);
                }

                const data = await response.json();
                console.log('Dados recebidos:', data);

                if (data.success) {
                    renderizarAulas(data.data);
                    renderizarPaginacao(data.pagination);
                    
                    // Extrair tags únicas para o filtro (apenas na primeira carga)
                    if (pagina === 1 && data.data.length > 0) {
                        extrairTags(data.data);
                    }
                } else {
                    alert('Erro ao carregar aulas: ' + (data.message || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro:', error);
                document.getElementById('listaAulas').innerHTML = `
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <p class="mt-2 text-red-500 font-medium">Erro ao carregar aulas</p>
                        <p class="mt-1 text-sm text-gray-500">${error.message}</p>
                        <button onclick="location.reload()" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Tentar novamente
                        </button>
                    </div>
                `;
            }
        }

        function renderizarAulas(aulas) {
            const container = document.getElementById('listaAulas');
            
            if (aulas.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="mt-2 text-gray-500">Nenhuma aula encontrada com os filtros selecionados</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = aulas.map(aula => `
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow cursor-pointer border border-gray-100" onclick="verDetalhes(${aula.id})">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900">${aula.data_hora}</h3>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusColor(aula.status)}">
                                        ${aula.status_nome}
                                    </span>
                                    ${aula.e_futura ? '<span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Próxima</span>' : ''}
                                </div>
                                <div class="mb-3">
                                    <span class="text-sm font-medium text-indigo-700 bg-indigo-100 px-3 py-1.5 rounded-lg inline-block">
                                        <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        ${aula.duracao_minutos} minutos
                                    </span>
                                </div>
                                
                                ${aula.conteudo_resumo ? `
                                    <p class="text-sm text-gray-700 mb-3">${aula.conteudo_resumo}</p>
                                ` : ''}

                                ${aula.tags && aula.tags.length > 0 ? `
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        ${aula.tags.map(tag => `
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: ${tag.cor}20; color: ${tag.cor};">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                </svg>
                                                ${tag.nome}
                                            </span>
                                        `).join('')}
                                    </div>
                                ` : ''}

                                <div class="flex gap-3">
                                    ${aula.tem_materiais ? '<span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> Materiais</span>' : ''}
                                    ${aula.tem_exercicios ? '<span class="text-xs px-2 py-1 bg-purple-100 text-purple-800 rounded flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg> Exercícios</span>' : ''}
                                    ${aula.tem_dificuldades ? '<span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> Dificuldades</span>' : ''}
                                </div>
                            </div>
                            <div class="ml-4">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function renderizarPaginacao(pagination) {
            const container = document.getElementById('paginacao');
            if (pagination.last_page <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '<div class="flex justify-center gap-2">';
            
            // Botão anterior
            if (pagination.current_page > 1) {
                html += `<button onclick="mudarPagina(${pagination.current_page - 1})" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Anterior</button>`;
            }

            // Páginas
            for (let i = 1; i <= pagination.last_page; i++) {
                if (i === pagination.current_page) {
                    html += `<button class="px-4 py-2 border border-indigo-500 bg-indigo-50 rounded-md text-sm font-medium text-indigo-600">${i}</button>`;
                } else if (i === 1 || i === pagination.last_page || (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)) {
                    html += `<button onclick="mudarPagina(${i})" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">${i}</button>`;
                } else if (i === pagination.current_page - 3 || i === pagination.current_page + 3) {
                    html += '<span class="px-2 py-2">...</span>';
                }
            }

            // Botão próximo
            if (pagination.current_page < pagination.last_page) {
                html += `<button onclick="mudarPagina(${pagination.current_page + 1})" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Próximo</button>`;
            }

            html += '</div>';
            container.innerHTML = html;
        }

        function mudarPagina(pagina) {
            paginaAtual = pagina;
            carregarAulas(pagina);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function getStatusColor(status) {
            const cores = {
                'realizada': 'bg-green-100 text-green-800',
                'pendente': 'bg-blue-100 text-blue-800',
                'reagendada': 'bg-yellow-100 text-yellow-800',
                'cancelada': 'bg-red-100 text-red-800',
                'falta': 'bg-gray-100 text-gray-800',
            };
            return cores[status] || 'bg-gray-100 text-gray-800';
        }

        function verDetalhes(aulaId) {
            window.location.href = `/aluno/aulas/${aulaId}`;
        }

        function extrairTags(aulas) {
            const tagsMap = new Map();
            aulas.forEach(aula => {
                if (aula.tags && aula.tags.length > 0) {
                    aula.tags.forEach(tag => {
                        if (!tagsMap.has(tag.id)) {
                            tagsMap.set(tag.id, tag);
                        }
                    });
                }
            });

            const select = document.getElementById('filtroTag');
            tagsMap.forEach(tag => {
                const option = document.createElement('option');
                option.value = tag.id;
                option.textContent = tag.nome;
                select.appendChild(option);
            });
        }

        // Carregar tags para o filtro
        async function carregarTags() {
            // As tags serão carregadas da primeira requisição de aulas
            // Não precisa fazer requisição separada
        }

        // Inicializar
        carregarAulas();
    </script>
</x-aluno-layout>
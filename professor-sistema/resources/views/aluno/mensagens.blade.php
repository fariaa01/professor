<x-aluno-layout>
    <div class="px-4 sm:px-6 lg:px-8" style="max-width: 1400px; margin: 0 auto;">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Mensagens</h1>
            <p class="text-sm text-gray-600 mt-1" id="professorNome">Converse com seu professor</p>
        </div>

        <!-- Container do Chat -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden" style="height: calc(100vh - 250px);">
            <!-- Área de Mensagens -->
            <div id="mensagensContainer" class="p-6 overflow-y-auto" style="height: calc(100% - 80px);">
                <div id="loading" class="flex items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                    <p class="ml-3 text-gray-500">Carregando mensagens...</p>
                </div>
                <div id="mensagens" class="space-y-4 hidden"></div>
            </div>

            <!-- Formulário de Envio -->
            <div class="border-t border-gray-200 p-4 bg-white">
                <form id="formMensagem" class="flex gap-3 items-end">
                    <div class="flex-1 relative">
                        <textarea 
                            id="inputMensagem" 
                            rows="1" 
                            placeholder="Digite sua mensagem... (Enter para enviar, Shift+Enter para nova linha)"
                            class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 resize-none px-4 py-3 pr-12 shadow-sm bg-white"
                            style="min-height: 50px; max-height: 150px;"></textarea>
                        <div class="absolute right-3 bottom-3 text-xs text-gray-400" id="charCounter">0/5000</div>
                    </div>
                    <button 
                        type="submit"
                        id="btnEnviar"
                        style="padding: 0.75rem 1.25rem; background: linear-gradient(to right, #4f46e5, #4338ca); color: white; border-radius: 0.75rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); display: flex; align-items: center; gap: 0.5rem; font-weight: 500; transition: all 0.2s; cursor: pointer; border: none; height: 50px;"
                        onmouseover="this.style.background='linear-gradient(to right, #4338ca, #3730a3)'"
                        onmouseout="this.style.background='linear-gradient(to right, #4f46e5, #4338ca)'"
                        class="focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <span>Enviar</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
    </style>

    <script>
        const API_BASE_URL = 'http://localhost:8000/api/aluno';
        const token = localStorage.getItem('aluno_token');
        let ultimaMensagemId = null;
        let pollingInterval = null;

        if (!token) {
            window.location.href = '/aluno/login';
        }

        // Auto-resize do textarea
        const textarea = document.getElementById('inputMensagem');
        const charCounter = document.getElementById('charCounter');
        const btnEnviar = document.getElementById('btnEnviar');

        textarea.addEventListener('input', function() {
            this.style.height = '50px';
            this.style.height = Math.min(this.scrollHeight, 150) + 'px';
            
            // Atualizar contador de caracteres
            const length = this.value.length;
            charCounter.textContent = length + '/5000';
            if (length > 4500) {
                charCounter.className = 'absolute right-3 bottom-3 text-xs text-red-500 font-medium';
            } else {
                charCounter.className = 'absolute right-3 bottom-3 text-xs text-gray-400';
            }
            
            // Habilitar/desabilitar botão
            btnEnviar.disabled = this.value.trim().length === 0;
        });

        // Permitir enviar com Enter (Shift+Enter para nova linha)
        textarea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                document.getElementById('formMensagem').dispatchEvent(new Event('submit'));
            }
        });

        // Desabilitar botão inicialmente
        btnEnviar.disabled = true;

        // Carregar mensagens
        async function carregarChat() {
            try {
                const response = await fetch(API_BASE_URL + '/mensagens', {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + token
                    }
                });

                const data = await response.json();

                if (data.success) {
                    if (data.data.professor) {
                        document.getElementById('professorNome').textContent = 'Conversa com ' + data.data.professor.nome;
                    }
                    
                    renderizarMensagens(data.data.mensagens);
                    marcarComoLidas();
                    
                    if (data.data.mensagens.length > 0) {
                        ultimaMensagemId = data.data.mensagens[data.data.mensagens.length - 1].id;
                    }
                }
            } catch (error) {
                console.error('Erro ao carregar chat:', error);
            }
        }

        // Renderizar mensagens
        function renderizarMensagens(mensagens) {
            const container = document.getElementById('mensagens');
            const loading = document.getElementById('loading');

            if (mensagens.length === 0) {
                container.innerHTML = '<div class="text-center py-12 text-gray-500">' +
                    '<svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>' +
                    '</svg>' +
                    '<p class="text-lg font-medium">Nenhuma mensagem ainda</p>' +
                    '<p class="text-sm mt-1">Envie uma mensagem para iniciar a conversa</p>' +
                    '</div>';
            } else {
                let html = '';
                let ultimaData = null;

                mensagens.forEach(function(msg) {
                    // Separador de data
                    if (msg.data_formatada !== ultimaData) {
                        html += '<div class="flex items-center justify-center my-4">' +
                            '<div class="bg-gray-200 text-gray-600 text-xs px-3 py-1 rounded-full">' +
                            msg.data_formatada +
                            '</div>' +
                            '</div>';
                        ultimaData = msg.data_formatada;
                    }

                    // Mensagem
                    const isAluno = msg.remetente === 'aluno';
                    const align = isAluno ? 'justify-end' : 'justify-start';
                    const bgStyle = isAluno ? 'background: linear-gradient(to bottom right, #2563eb, #1d4ed8); color: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);' : 'background: #f3f4f6; color: #111827; border: 1px solid #e5e7eb;';

                    html += '<div class="flex ' + align + '" style="animation: fade-in 0.3s ease-out;">' +
                        '<div style="max-width: 70%;">' +
                        '<div style="' + bgStyle + ' border-radius: 1rem; padding: 0.75rem 1rem; word-break: break-word;">' +
                        '<p style="font-size: 0.875rem; white-space: pre-wrap; line-height: 1.5;">' + escapeHtml(msg.mensagem) + '</p>' +
                        '</div>' +
                        '<div class="flex items-center gap-2 mt-1 ' + (isAluno ? 'justify-end' : 'justify-start') + '">' +
                        '<p class="text-xs text-gray-500">' + msg.horario + '</p>' +
                        (isAluno && msg.lida ? '<svg style="width: 0.75rem; height: 0.75rem; color: #3b82f6;" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>' : '') +
                        '</div>' +
                        '</div>' +
                        '</div>';
                });

                container.innerHTML = html;
            }

            loading.classList.add('hidden');
            container.classList.remove('hidden');
            scrollParaFinal();
        }

        // Enviar mensagem
        document.getElementById('formMensagem').addEventListener('submit', async function(e) {
            e.preventDefault();

            const mensagem = textarea.value.trim();
            if (!mensagem) return;

            const button = document.getElementById('btnEnviar');
            const originalHtml = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

            try {
                const response = await fetch(API_BASE_URL + '/mensagens', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({
                        mensagem: mensagem
                    })
                });

                const data = await response.json();

                if (data.success) {
                    adicionarMensagem(data.data);
                    textarea.value = '';
                    textarea.style.height = '50px';
                    charCounter.textContent = '0/5000';
                    ultimaMensagemId = data.data.id;
                }
            } catch (error) {
                console.error('Erro ao enviar mensagem:', error);
                alert('Erro ao enviar mensagem. Tente novamente.');
            } finally {
                button.innerHTML = originalHtml;
                button.disabled = true;
                textarea.focus();
            }
        });

        // Adicionar mensagem ao chat
        function adicionarMensagem(msg) {
            const container = document.getElementById('mensagens');
            const emptyState = container.querySelector('.text-center');
            if (emptyState) {
                container.innerHTML = '';
            }

            const isAluno = msg.remetente === 'aluno';
            const align = isAluno ? 'justify-end' : 'justify-start';
            const bgStyle = isAluno ? 'background: linear-gradient(to bottom right, #2563eb, #1d4ed8); color: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);' : 'background: #f3f4f6; color: #111827; border: 1px solid #e5e7eb;';

            const html = '<div class="flex ' + align + '" style="animation: fade-in 0.3s ease-out;">' +
                '<div style="max-width: 70%;">' +
                '<div style="' + bgStyle + ' border-radius: 1rem; padding: 0.75rem 1rem; word-break: break-word;">' +
                '<p style="font-size: 0.875rem; white-space: pre-wrap; line-height: 1.5;">' + escapeHtml(msg.mensagem) + '</p>' +
                '</div>' +
                '<div class="flex items-center gap-2 mt-1 ' + (isAluno ? 'justify-end' : 'justify-start') + '">' +
                '<p class="text-xs text-gray-500">' + msg.horario + '</p>' +
                '</div>' +
                '</div>' +
                '</div>';

            container.insertAdjacentHTML('beforeend', html);
            scrollParaFinal();
        }

        // Marcar mensagens como lidas
        async function marcarComoLidas() {
            try {
                await fetch(API_BASE_URL + '/mensagens/marcar-lidas', {
                    method: 'PATCH',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + token
                    }
                });
            } catch (error) {
                console.error('Erro ao marcar mensagens:', error);
            }
        }

        // Polling para novas mensagens
        async function verificarNovasMensagens() {
            try {
                const response = await fetch(API_BASE_URL + '/mensagens', {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + token
                    }
                });

                const data = await response.json();

                if (data.success && data.data.mensagens.length > 0) {
                    const novasMensagens = data.data.mensagens.filter(function(msg) {
                        return ultimaMensagemId === null || msg.id > ultimaMensagemId;
                    });

                    novasMensagens.forEach(function(msg) {
                        adicionarMensagem(msg);
                    });

                    if (novasMensagens.length > 0) {
                        ultimaMensagemId = data.data.mensagens[data.data.mensagens.length - 1].id;
                        marcarComoLidas();
                    }
                }
            } catch (error) {
                console.error('Erro ao verificar mensagens:', error);
            }
        }

        // Scroll para o final
        function scrollParaFinal() {
            const container = document.getElementById('mensagensContainer');
            container.scrollTop = container.scrollHeight;
        }

        // Escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Inicializar
        carregarChat();
        pollingInterval = setInterval(verificarNovasMensagens, 3000);

        // Limpar interval ao sair
        window.addEventListener('beforeunload', function() {
            if (pollingInterval) {
                clearInterval(pollingInterval);
            }
        });
    </script>
</x-aluno-layout>

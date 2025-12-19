<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-50 to-indigo-100" style="max-width: 1400px; margin: 0 auto; width: 100%;">
        <!-- Logo/Header -->
        <div class="mb-6">
            <div class="flex items-center justify-center space-x-3">
                <div class="w-14 h-14 bg-blue-600 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Portal do Aluno</h1>
                    <p class="text-base text-gray-600">Sistema de Acompanhamento de Aulas</p>
                </div>
            </div>
        </div>

        <!-- Card de Login -->
        <div class="w-full sm:max-w-4xl px-10 py-10 bg-white shadow-lg overflow-hidden sm:rounded-xl border border-gray-200">
            <!-- Formulário -->
            <form id="loginForm" class="space-y-4 max-w-md mx-auto">
                <!-- Email -->
                <div>
                    <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        required
                        placeholder="seu@email.com"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <label for="password" class="block font-medium text-sm text-gray-700">Senha</label>
                    <input 
                        type="password" 
                        id="password" 
                        required
                        placeholder="••••••"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                </div>

                <!-- Mensagem de erro -->
                <div id="errorMessage" class="hidden p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-red-800"></p>
                    </div>
                </div>

                <!-- Mensagem de sucesso -->
                <div id="successMessage" class="hidden p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-green-800"></p>
                    </div>
                </div>

                <!-- Botão Entrar -->
                <div class="flex items-center justify-end mt-4">
                    <button type="submit" id="loginButton" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Entrar
                    </button>
                </div>
            </form>

            <!-- Credenciais de teste -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-500 mb-3">Credenciais de teste:</p>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <button onclick="fillCredentials('maria@exemplo.com', 'mariae')" class="p-2 bg-gray-50 hover:bg-gray-100 rounded text-left transition border border-gray-200">
                        <strong class="text-gray-900">Maria Santos</strong><br>
                        <span class="text-gray-600">maria@exemplo.com</span>
                    </button>
                    <button onclick="fillCredentials('pedro@exemplo.com', 'pedroe')" class="p-2 bg-gray-50 hover:bg-gray-100 rounded text-left transition border border-gray-200">
                        <strong class="text-gray-900">Pedro Costa</strong><br>
                        <span class="text-gray-600">pedro@exemplo.com</span>
                    </button>
                    <button onclick="fillCredentials('ana@exemplo.com', 'anaexe')" class="p-2 bg-gray-50 hover:bg-gray-100 rounded text-left transition border border-gray-200">
                        <strong class="text-gray-900">Ana Silva</strong><br>
                        <span class="text-gray-600">ana@exemplo.com</span>
                    </button>
                    <button onclick="fillCredentials('lucas@exemplo.com', 'lucase')" class="p-2 bg-gray-50 hover:bg-gray-100 rounded text-left transition border border-gray-200">
                        <strong class="text-gray-900">Lucas Oliveira</strong><br>
                        <span class="text-gray-600">lucas@exemplo.com</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center text-sm text-gray-600">
            <p>&copy; {{ date('Y') }} Professor System. Todos os direitos reservados.</p>
        </div>
    </div>
                            <span class="text-gray-600">lucas@exemplo.com</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const API_BASE_URL = 'http://localhost:8000/api/aluno';
        let authToken = null;

        // Preencher credenciais de teste
        function fillCredentials(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }

        // Mostrar erro
        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.querySelector('p').textContent = message;
            errorDiv.classList.remove('hidden');
            document.getElementById('successMessage').classList.add('hidden');
        }

        // Mostrar sucesso
        function showSuccess(message) {
            const successDiv = document.getElementById('successMessage');
            successDiv.querySelector('p').textContent = message;
            successDiv.classList.remove('hidden');
            document.getElementById('errorMessage').classList.add('hidden');
        }

        // Login
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const button = document.getElementById('loginButton');

            button.disabled = true;
            button.textContent = 'Entrando...';

            try {
                const response = await fetch(`${API_BASE_URL}/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (data.success) {
                    authToken = data.data.access_token;
                    localStorage.setItem('aluno_token', authToken);
                    localStorage.setItem('aluno_data', JSON.stringify(data.data.aluno));
                    
                    showSuccess(`Bem-vindo(a), ${data.data.aluno.nome}!`);
                    
                    setTimeout(() => {
                        window.location.href = '{{ route("aluno.dashboard") }}';
                    }, 1000);
                } else {
                    showError(data.message || 'Erro ao fazer login');
                }
            } catch (error) {
                showError('Erro de conexão com o servidor');
                console.error(error);
            } finally {
                button.disabled = false;
                button.textContent = 'Entrar';
            }
        });
    </script>
</x-guest-layout>

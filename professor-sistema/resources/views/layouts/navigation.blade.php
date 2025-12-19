<nav class="bg-white border-b border-gray-200 shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center space-x-2">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 group">
                    <svg class="w-8 h-8 text-blue-600 group-hover:text-blue-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span class="text-xl font-bold text-gray-900 group-hover:text-blue-700 transition-colors">Professor System</span>
                </a>
            </div>

            <!-- Navigation Items -->
            <div class="flex items-center space-x-1">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('dashboard') ? 'text-blue-700 bg-blue-50' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
                    Dashboard
                </a>

                <!-- Gestão (Dropdown) -->
                <div x-data="{ open: false }" @click.away="open = false" class="relative">
                    <button @click="open = !open"
                            type="button"
                            class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 flex items-center {{ request()->routeIs(['alunos.*', 'tags.*', 'conteudos.*']) ? 'text-blue-700 bg-blue-50' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
                        Gestão
                        <svg class="ml-1 h-4 w-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    
                    <div x-show="open"
                         x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute left-0 mt-2 w-56 origin-top-left rounded-lg shadow-xl bg-white ring-1 ring-black ring-opacity-5 z-50">
                        <div class="py-1">
                            <a href="#" 
                               class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <div class="font-medium">Funcionários</div>
                                <div class="text-xs text-gray-500">Gerenciar equipe</div>
                            </a>
                            <a href="{{ route('alunos.index') }}" 
                               class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('alunos.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                                <div class="font-medium">Alunos</div>
                                <div class="text-xs text-gray-500">Gerenciar alunos</div>
                            </a>
                            <a href="{{ route('conteudos.index') }}" 
                               class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('conteudos.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                                <div class="font-medium">Conteúdos</div>
                                <div class="text-xs text-gray-500">Vídeos e materiais</div>
                            </a>
                            <a href="#" 
                               class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <div class="font-medium">Financeiro</div>
                                <div class="text-xs text-gray-500">Pagamentos e cobranças</div>
                            </a>
                            <a href="#" 
                               class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <div class="font-medium">Estoque</div>
                                <div class="text-xs text-gray-500">Materiais didáticos</div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Calendário -->
                <a href="{{ route('calendario.index') }}" 
                   class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('calendario.*') ? 'text-blue-700 bg-blue-50' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
                    Calendário
                </a>

                <!-- Aulas -->
                <a href="{{ route('aulas.index') }}" 
                   class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('aulas.*') ? 'text-blue-700 bg-blue-50' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
                    Aulas
                </a>

                <!-- Reuniões -->
                <a href="{{ route('meetings.index') }}" 
                   class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('meetings.*') ? 'text-blue-700 bg-blue-50' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
                    Reuniões
                </a>

                <!-- Relatórios -->
                <a href="{{ route('relatorios.index') }}" 
                   class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('relatorios.*') ? 'text-blue-700 bg-blue-50' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
                    Relatórios
                </a>

                <!-- User Menu (Dropdown) -->
                <div x-data="{ open: false }" @click.away="open = false" class="relative ml-4">
                    <button @click="open = !open"
                            type="button"
                            class="flex items-center space-x-2 px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <span class="hidden md:inline">{{ explode(' ', Auth::user()->name)[0] }}</span>
                        <svg class="h-4 w-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    
                    <div x-show="open"
                         x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-64 origin-top-right rounded-lg shadow-xl bg-white ring-1 ring-black ring-opacity-5 z-50">
                        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                            <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate mt-0.5">{{ Auth::user()->email }}</p>
                        </div>

                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" 
                               class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <div>
                                    <div class="font-medium">Meu Perfil</div>
                                    <div class="text-xs text-gray-500">Configurações da conta</div>
                                </div>
                            </a>
                        </div>

                        <div class="border-t border-gray-100"></div>

                        <div class="py-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="flex items-center w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors">
                                    <svg class="w-5 h-5 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    <div>
                                        <div class="font-medium text-red-600">Sair</div>
                                        <div class="text-xs text-gray-500">Encerrar sessão</div>
                                    </div>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

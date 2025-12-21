@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Conteúdos</h1>
                    <p class="text-sm text-gray-500 mt-1">Gerencie o material didático disponível para seus alunos</p>
                </div>
                <a href="{{ route('conteudos.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Novo Conteúdo
                </a>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if(session('success'))
            <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        @if($conteudos->isEmpty())
            <!-- Empty State -->
            <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 text-gray-300">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Nenhum conteúdo cadastrado</h3>
                <p class="text-sm text-gray-500 mb-6 max-w-md mx-auto">Comece criando seu primeiro conteúdo para compartilhar com seus alunos</p>
                <a href="{{ route('conteudos.create') }}" 
                   class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-150">
                    Criar Primeiro Conteúdo
                </a>
            </div>
        @else
            <!-- Content List -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="divide-y divide-gray-100">
                    @foreach($conteudos as $conteudo)
                        @php
                            $total = count($conteudo->alunos_ids ?? []);
                            $iniciados = $conteudo->progressos->where('iniciado_em', '!=', null)->count();
                            $concluidos = $conteudo->progressos->where('completo', true)->count();
                            $percentual = $total > 0 ? round(($concluidos / $total) * 100) : 0;
                        @endphp
                        
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                            <div class="flex items-center justify-between gap-4">
                                <!-- Left: Main Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3 mb-2">
                                        <!-- Type Icon -->
                                        <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center
                                            {{ $conteudo->tipo === 'video' ? 'bg-blue-50 text-blue-600' : 
                                               ($conteudo->tipo === 'pdf' ? 'bg-red-50 text-red-600' : 
                                               'bg-purple-50 text-purple-600') }}">
                                            @if($conteudo->tipo === 'video')
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path>
                                                </svg>
                                            @elseif($conteudo->tipo === 'pdf')
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"></path>
                                                </svg>
                                            @endif
                                        </div>
                                        
                                        <!-- Title and Meta -->
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-sm font-semibold text-gray-900 truncate">
                                                {{ $conteudo->titulo }}
                                            </h3>
                                            <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                    </svg>
                                                    {{ $total }} {{ $total === 1 ? 'aluno' : 'alunos' }}
                                                </span>
                                                @if($conteudo->duracao_segundos)
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        {{ $conteudo->duracao_formatada }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Progress Bar -->
                                    @if($total > 0)
                                        <div class="ml-13 mt-2">
                                            <div class="flex items-center gap-2">
                                                <div class="flex-1 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                                    <div class="bg-green-500 h-full rounded-full transition-all duration-300" 
                                                         style="width: {{ $percentual }}%"></div>
                                                </div>
                                                <span class="text-xs font-medium text-gray-600 min-w-[3rem] text-right">
                                                    {{ $percentual }}%
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $concluidos }} concluíram · {{ $iniciados }} iniciaram
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Right: Status and Actions -->
                                <div class="flex items-center gap-3">
                                    <!-- Status Badge -->
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                        {{ $conteudo->status === 'publicado' ? 'bg-green-50 text-green-700 border border-green-200' : 
                                           ($conteudo->status === 'rascunho' ? 'bg-yellow-50 text-yellow-700 border border-yellow-200' : 
                                           'bg-gray-50 text-gray-700 border border-gray-200') }}">
                                        {{ ucfirst($conteudo->status) }}
                                    </span>

                                    <!-- Actions -->
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('conteudos.show', $conteudo) }}" 
                                           class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-150"
                                           title="Ver detalhes">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('conteudos.edit', $conteudo) }}" 
                                           class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors duration-150"
                                           title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            @if($conteudos->hasPages())
                <div class="mt-4">
                    {{ $conteudos->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

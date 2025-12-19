@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex items-center gap-4">
            <x-button variant="ghost" :href="route('aulas.show', $aula)">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </x-button>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Editar Registro Pedagógico</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $aula->aluno->nome }} - {{ $aula->data_hora->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <!-- Formulário -->
        <form method="POST" action="{{ route('aulas.update', $aula) }}">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Conteúdo da Aula -->
                <x-card>
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <label class="text-lg font-semibold text-gray-900">Conteúdo Trabalhado</label>
                    </div>
                    <textarea 
                        name="conteudo" 
                        rows="5"
                        placeholder="Descreva os assuntos e tópicos que foram trabalhados durante a aula..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >{{ old('conteudo', $aula->conteudo) }}</textarea>
                    <p class="mt-2 text-xs text-gray-500">Descreva os temas, conceitos e assuntos abordados nesta aula</p>
                    @error('conteudo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </x-card>

                <!-- Materiais Utilizados -->
                <x-card>
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <label class="text-lg font-semibold text-gray-900">Materiais Utilizados</label>
                    </div>
                    <textarea 
                        name="materiais" 
                        rows="3"
                        placeholder="Liste os materiais, recursos, livros ou ferramentas utilizados..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >{{ old('materiais', $aula->materiais) }}</textarea>
                    <p class="mt-2 text-xs text-gray-500">Livros, apostilas, vídeos, sites, jogos educativos, etc.</p>
                    @error('materiais')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </x-card>

                <!-- Exercícios Aplicados -->
                <x-card>
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <label class="text-lg font-semibold text-gray-900">Exercícios Aplicados</label>
                    </div>
                    <textarea 
                        name="exercicios" 
                        rows="4"
                        placeholder="Descreva os exercícios, atividades práticas ou tarefas realizadas..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >{{ old('exercicios', $aula->exercicios) }}</textarea>
                    <p class="mt-2 text-xs text-gray-500">Liste e descreva os exercícios feitos durante ou solicitados para casa</p>
                    @error('exercicios')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </x-card>

                <!-- Dificuldades Encontradas -->
                <x-card>
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <label class="text-lg font-semibold text-gray-900">Dificuldades Encontradas</label>
                    </div>
                    <textarea 
                        name="dificuldades" 
                        rows="3"
                        placeholder="Anote as dificuldades, dúvidas recorrentes ou problemas de compreensão..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >{{ old('dificuldades', $aula->dificuldades) }}</textarea>
                    <p class="mt-2 text-xs text-gray-500">Registre os pontos em que o aluno apresentou mais dificuldade</p>
                    @error('dificuldades')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </x-card>

                <!-- Pontos de Atenção -->
                <x-card>
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <label class="text-lg font-semibold text-gray-900">Pontos de Atenção</label>
                    </div>
                    <textarea 
                        name="pontos_atencao" 
                        rows="3"
                        placeholder="Anote aspectos importantes para reforçar nas próximas aulas..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >{{ old('pontos_atencao', $aula->pontos_atencao) }}</textarea>
                    <p class="mt-2 text-xs text-gray-500">Tópicos que precisam ser revisados ou reforçados futuramente</p>
                    @error('pontos_atencao')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </x-card>

                <!-- Observações Gerais -->
                <x-card>
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                        <label class="text-lg font-semibold text-gray-900">Observações Gerais</label>
                    </div>
                    <textarea 
                        name="observacoes" 
                        rows="3"
                        placeholder="Outras observações importantes sobre a aula ou o desempenho do aluno..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >{{ old('observacoes', $aula->observacoes) }}</textarea>
                    <p class="mt-2 text-xs text-gray-500">Anotações gerais, evolução do aluno, comportamento, etc.</p>
                    @error('observacoes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </x-card>

                <!-- Botões de Ação -->
                <x-card>
                    <div class="flex items-center justify-between">
                        <x-button variant="ghost" :href="route('aulas.show', $aula)" type="button">
                            Cancelar
                        </x-button>
                        <x-button variant="primary" type="submit">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Salvar Registro
                        </x-button>
                    </div>
                </x-card>
            </div>
        </form>
    </div>
</div>
@endsection

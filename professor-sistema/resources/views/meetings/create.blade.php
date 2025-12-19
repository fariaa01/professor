@extends('layouts.app')

@section('content')
<div class="py-8">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Criar Reunião Online
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <!-- Ação Rápida -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 mb-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold mb-2">🚀 Reunião Rápida</h3>
                        <p class="text-blue-100">Inicie uma reunião instantânea sem agendamento</p>
                    </div>
                    <form action="{{ route('meetings.store') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="title" value="Reunião Rápida - {{ now()->format('d/m/Y H:i') }}">
                        <button type="submit" 
                                class="bg-white text-blue-600 font-bold px-6 py-3 rounded-lg hover:bg-blue-50 transition shadow-lg">
                            Iniciar Agora
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-blue-500">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4">Ou agende uma reunião personalizada</h3>
                    
                    <form action="{{ route('meetings.store') }}" method="POST">
                        @csrf

                        <!-- Título -->
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Título da Reunião *
                            </label>
                            <input type="text" name="title" id="title" required
                                   placeholder="Ex: Aula de Matemática com João"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="{{ old('title') }}">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Aluno -->
                        <div class="mb-4">
                            <label for="aluno_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Selecione o Aluno
                            </label>
                            <select name="aluno_id" id="aluno_id"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Reunião sem aluno específico</option>
                                @foreach($alunos as $aluno)
                                    <option value="{{ $aluno->id }}" {{ old('aluno_id') == $aluno->id ? 'selected' : '' }}>
                                        {{ $aluno->nome }} @if($aluno->email) - {{ $aluno->email }} @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('aluno_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Data e Hora -->
                        <div class="mb-4">
                            <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-2">
                                Agendar para (deixe vazio para iniciar agora)
                            </label>
                            <input type="datetime-local" name="scheduled_at" id="scheduled_at"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="{{ old('scheduled_at') }}">
                            @error('scheduled_at')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Descrição -->
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Descrição (opcional)
                            </label>
                            <textarea name="description" id="description" rows="3"
                                      placeholder="Adicione observações ou tópicos a serem abordados..."
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex gap-3">
                            <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition">
                                Criar Reunião
                            </button>
                            <a href="{{ route('meetings.index') }}"
                               class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg transition">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Dicas -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-semibold text-blue-900 mb-2">💡 Dicas:</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• Use <strong>Reunião Rápida</strong> para iniciar instantaneamente</li>
                    <li>• Selecione um aluno para enviar o link da reunião automaticamente</li>
                    <li>• Deixe a data vazia para entrar na sala imediatamente após criar</li>
                    <li>• Você pode compartilhar o link da sala com qualquer aluno depois</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

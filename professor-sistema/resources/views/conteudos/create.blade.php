@extends('layouts.app')

@section('content')
<div class="py-8">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Criar Novo Conteúdo
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-blue-500">
                <div class="p-8">
                    <form action="{{ route('conteudos.store') }}" method="POST">
                        @csrf

                        <!-- Título -->
                        <div class="mb-6">
                            <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">
                                Título do Conteúdo *
                            </label>
                            <input type="text" name="titulo" id="titulo" required
                                   placeholder="Ex: Introdução à Álgebra Linear"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="{{ old('titulo') }}">
                            @error('titulo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tipo de Conteúdo -->
                        <div class="mb-6">
                            <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Conteúdo *
                            </label>
                            <select name="tipo" id="tipo" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    onchange="toggleUrlField()">
                                <option value="video" {{ old('tipo') === 'video' ? 'selected' : '' }}>Vídeo (YouTube, Vimeo)</option>
                                <option value="link" {{ old('tipo') === 'link' ? 'selected' : '' }}>Link Externo</option>
                                <option value="pdf" {{ old('tipo') === 'pdf' ? 'selected' : '' }}>Material PDF</option>
                                <option value="texto" {{ old('tipo') === 'texto' ? 'selected' : '' }}>Texto/Artigo</option>
                            </select>
                            @error('tipo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- URL -->
                        <div class="mb-6" id="url-field">
                            <label for="url" class="block text-sm font-medium text-gray-700 mb-2">
                                Link do Vídeo/Conteúdo *
                            </label>
                            <input type="url" name="url" id="url"
                                   placeholder="https://www.youtube.com/watch?v=..."
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="{{ old('url') }}">
                            <p class="mt-1 text-xs text-gray-500">
                                Para YouTube: Use vídeos não listados para privacidade. Para Vimeo: Configure as permissões adequadas.
                            </p>
                            @error('url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Duração (apenas para vídeos) -->
                        <div class="mb-6" id="duracao-field">
                            <label for="duracao_minutos" class="block text-sm font-medium text-gray-700 mb-2">
                                Duração do Vídeo (em minutos)
                            </label>
                            <input type="number" name="duracao_minutos" id="duracao_minutos" min="0"
                                   placeholder="Ex: 15"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="{{ old('duracao_minutos') }}">
                            <input type="hidden" name="duracao_segundos" id="duracao_segundos">
                            @error('duracao_segundos')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Descrição -->
                        <div class="mb-6">
                            <label for="descricao" class="block text-sm font-medium text-gray-700 mb-2">
                                Descrição
                            </label>
                            <textarea name="descricao" id="descricao" rows="4"
                                      placeholder="Descreva sobre o que é este conteúdo e o que os alunos aprenderão..."
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('descricao') }}</textarea>
                            @error('descricao')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Observações do Professor -->
                        <div class="mb-6">
                            <label for="observacoes" class="block text-sm font-medium text-gray-700 mb-2">
                                Observações e Orientações
                            </label>
                            <textarea name="observacoes" id="observacoes" rows="3"
                                      placeholder="Adicione dicas, recomendações ou pontos importantes para o aluno..."
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('observacoes') }}</textarea>
                            @error('observacoes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Selecionar Alunos -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Disponível para os alunos: *
                            </label>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 max-h-60 overflow-y-auto">
                                @foreach($alunos as $aluno)
                                    <label class="flex items-center py-2 hover:bg-gray-100 px-2 rounded cursor-pointer">
                                        <input type="checkbox" name="alunos_ids[]" value="{{ $aluno->id }}"
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                               {{ is_array(old('alunos_ids')) && in_array($aluno->id, old('alunos_ids')) ? 'checked' : '' }}>
                                        <span class="ml-3 text-sm text-gray-700">{{ $aluno->nome }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('alunos_ids')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-6">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status *
                            </label>
                            <select name="status" id="status" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="publicado" {{ old('status') === 'publicado' ? 'selected' : '' }}>Publicado (visível para alunos)</option>
                                <option value="rascunho" {{ old('status') === 'rascunho' ? 'selected' : '' }}>Rascunho (não visível)</option>
                            </select>
                        </div>

                        <!-- Botões -->
                        <div class="flex gap-3">
                            <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition">
                                Criar Conteúdo
                            </button>
                            <a href="{{ route('conteudos.index') }}"
                               class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg transition">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Converte minutos para segundos antes de enviar
        document.querySelector('form').addEventListener('submit', function(e) {
            const minutos = document.getElementById('duracao_minutos').value;
            if (minutos) {
                document.getElementById('duracao_segundos').value = minutos * 60;
            }
        });

        // Toggle campo URL baseado no tipo
        function toggleUrlField() {
            const tipo = document.getElementById('tipo').value;
            const urlField = document.getElementById('url-field');
            const duracaoField = document.getElementById('duracao-field');
            const urlInput = document.getElementById('url');

            if (tipo === 'texto') {
                urlField.style.display = 'none';
                urlInput.removeAttribute('required');
            } else {
                urlField.style.display = 'block';
                urlInput.setAttribute('required', 'required');
            }

            if (tipo === 'video') {
                duracaoField.style.display = 'block';
            } else {
                duracaoField.style.display = 'none';
            }
        }

        // Inicializa
        toggleUrlField();
    </script>
</div>
@endsection

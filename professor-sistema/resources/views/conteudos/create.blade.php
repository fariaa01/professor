@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">

    <!-- Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-5xl mx-auto px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Novo Conteúdo</h1>
                    <p class="text-sm text-gray-500 mt-3">
                        Crie e compartilhe material didático com seus alunos
                    </p>
                </div>

                <a href="{{ route('conteudos.index') }}"
                   class="inline-flex items-center px-5 py-3 text-sm font-medium
                          text-gray-700 bg-white border border-gray-300
                          rounded-xl hover:bg-gray-50 transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancelar
                </a>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-5xl mx-auto px-6 lg:px-8 py-12">
        <form action="{{ route('conteudos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Informações Básicas -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-14">
                <div class="px-8 py-6 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Informações Básicas</h3>
                </div>

                <div class="p-10 space-y-10">

                    <!-- Título -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Título do Conteúdo <span class="text-red-500">*</span>
                        </label>

                        <input type="text" name="titulo" required
                               placeholder="Ex: Introdução à Álgebra Linear"
                               value="{{ old('titulo') }}"
                               class="w-full px-5 py-3.5 border border-gray-300 rounded-xl
                                      focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                      transition-all">

                        @error('titulo')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tipo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-4">
                            Tipo de Conteúdo <span class="text-red-500">*</span>
                        </label>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">

                            <!-- Video -->
                            <label class="p-6 rounded-2xl border-2 border-gray-200 cursor-pointer
                                          hover:border-blue-500 transition-all
                                          has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                                <input type="radio" name="tipo" value="video" class="sr-only"
                                       {{ old('tipo','video') === 'video' ? 'checked' : '' }}
                                       onchange="updateContentType()">
                                <div class="text-center">
                                    <svg class="w-10 h-10 mx-auto mb-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                                    </svg>
                                    <p class="font-medium">Vídeo</p>
                                </div>
                            </label>

                            <!-- PDF -->
                            <label class="p-6 rounded-2xl border-2 border-gray-200 cursor-pointer
                                          hover:border-red-500 transition-all
                                          has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                                <input type="radio" name="tipo" value="pdf" class="sr-only"
                                       {{ old('tipo') === 'pdf' ? 'checked' : '' }}
                                       onchange="updateContentType()">
                                <div class="text-center">
                                    <svg class="w-10 h-10 mx-auto mb-3 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M4 4a2 2 0 012-2h4.586L15.414 6V16a2 2 0 01-2 2H6a2 2 0 01-2-2z"></path>
                                    </svg>
                                    <p class="font-medium">PDF</p>
                                </div>
                            </label>

                            <!-- Link -->
                            <label class="p-6 rounded-2xl border-2 border-gray-200 cursor-pointer
                                          hover:border-purple-500 transition-all
                                          has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50">
                                <input type="radio" name="tipo" value="link" class="sr-only"
                                       {{ old('tipo') === 'link' ? 'checked' : '' }}
                                       onchange="updateContentType()">
                                <div class="text-center">
                                    <svg class="w-10 h-10 mx-auto mb-3 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M12.586 4.586a2 2 0 112.828 2.828l-3 3"></path>
                                    </svg>
                                    <p class="font-medium">Link</p>
                                </div>
                            </label>

                            <!-- Texto -->
                            <label class="p-6 rounded-2xl border-2 border-gray-200 cursor-pointer
                                          hover:border-gray-500 transition-all
                                          has-[:checked]:border-gray-500 has-[:checked]:bg-gray-100">
                                <input type="radio" name="tipo" value="texto" class="sr-only"
                                       {{ old('tipo') === 'texto' ? 'checked' : '' }}
                                       onchange="updateContentType()">
                                <div class="text-center">
                                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M4 4a2 2 0 012-2h4.586L15.414 6V16z"></path>
                                    </svg>
                                    <p class="font-medium">Texto</p>
                                </div>
                            </label>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-end gap-6 pt-8 border-t border-gray-200">
                <a href="{{ route('conteudos.index') }}"
                   class="px-6 py-3 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 transition-all">
                    Cancelar
                </a>

                <button type="submit"
                        class="px-7 py-3 rounded-xl bg-blue-600 text-white font-medium
                               hover:bg-blue-700 shadow-md hover:shadow-lg transition-all">
                    Criar Conteúdo
                </button>
            </div>

        </form>
    </div>
</div>

<script>
function updateContentType() {}
</script>
@endsection

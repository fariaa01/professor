@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Tags</h1>
                <p class="mt-1 text-sm text-gray-600">Gerencie as etiquetas para categorizar seus alunos</p>
            </div>
            <x-button :href="route('tags.create')">
                + Nova Tag
            </x-button>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if($tags->isEmpty())
            <x-card>
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Nenhuma tag cadastrada</h3>
                    <p class="mt-1 text-sm text-gray-500">Comece criando sua primeira tag para organizar seus alunos.</p>
                    <div class="mt-6">
                        <x-button :href="route('tags.create')">
                            + Criar Tag
                        </x-button>
                    </div>
                </div>
            </x-card>
        @else
            <x-card>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tag
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Cor
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Alunos
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($tags as $tag)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <span 
                                                class="inline-block w-4 h-4 rounded-full" 
                                                style="background-color: {{ $tag->cor }};"
                                            ></span>
                                            <span class="text-sm font-medium text-gray-900">{{ $tag->nome }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $tag->cor }}</code>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-500">
                                            {{ $tag->alunos_count }} {{ $tag->alunos_count === 1 ? 'aluno' : 'alunos' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <x-button 
                                                variant="outline" 
                                                size="sm"
                                                :href="route('tags.edit', $tag)"
                                            >
                                                Editar
                                            </x-button>
                                            <form 
                                                method="POST" 
                                                action="{{ route('tags.destroy', $tag) }}"
                                                onsubmit="return confirm('Tem certeza que deseja excluir esta tag? Ela será removida de todos os alunos.');"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <x-button type="submit" variant="outline" size="sm" class="text-red-600 hover:text-red-700 hover:bg-red-50">
                                                    Excluir
                                                </x-button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        @endif
    </div>
</div>
@endsection

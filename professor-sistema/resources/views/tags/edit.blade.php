<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Editar Tag</h1>
            <p class="mt-1 text-sm text-gray-600">Atualize as informações da etiqueta</p>
        </div>

        <x-card>
            <form method="POST" action="{{ route('tags.update', $tag) }}" x-data="{ cor: '{{ old('cor', $tag->cor) }}' }">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div>
                        <x-input-label for="nome" value="Nome da Tag *" />
                        <x-input 
                            id="nome" 
                            name="nome" 
                            type="text" 
                            value="{{ old('nome', $tag->nome) }}" 
                            required 
                            autofocus 
                            placeholder="Ex: Iniciante, ENEM, Reforço..."
                        />
                        <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="cor" value="Cor *" />
                        <div class="flex items-center gap-4 mt-2">
                            <input 
                                type="color" 
                                id="cor" 
                                name="cor" 
                                x-model="cor"
                                value="{{ old('cor', $tag->cor) }}"
                                class="h-10 w-20 rounded border border-gray-300 cursor-pointer"
                                required
                            >
                            <div class="flex-1">
                                <div 
                                    class="px-4 py-2 rounded-lg border-2 transition-colors"
                                    :style="{ 
                                        backgroundColor: cor + '20', 
                                        borderColor: cor + '40',
                                        color: cor 
                                    }"
                                >
                                    <span class="font-medium">Prévia da Tag</span>
                                </div>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Escolha uma cor para identificar esta tag</p>
                        <x-input-error :messages="$errors->get('cor')" class="mt-2" />
                    </div>

                    <div class="pt-4">
                        <p class="text-sm font-medium text-gray-700 mb-3">Sugestões de cores:</p>
                        <div class="flex flex-wrap gap-2">
                            <button 
                                type="button" 
                                @click="cor = '#10b981'" 
                                class="w-8 h-8 rounded-full border-2 border-gray-200 hover:scale-110 transition-transform"
                                style="background-color: #10b981;"
                                title="Verde"
                            ></button>
                            <button 
                                type="button" 
                                @click="cor = '#3b82f6'" 
                                class="w-8 h-8 rounded-full border-2 border-gray-200 hover:scale-110 transition-transform"
                                style="background-color: #3b82f6;"
                                title="Azul"
                            ></button>
                            <button 
                                type="button" 
                                @click="cor = '#8b5cf6'" 
                                class="w-8 h-8 rounded-full border-2 border-gray-200 hover:scale-110 transition-transform"
                                style="background-color: #8b5cf6;"
                                title="Roxo"
                            ></button>
                            <button 
                                type="button" 
                                @click="cor = '#ef4444'" 
                                class="w-8 h-8 rounded-full border-2 border-gray-200 hover:scale-110 transition-transform"
                                style="background-color: #ef4444;"
                                title="Vermelho"
                            ></button>
                            <button 
                                type="button" 
                                @click="cor = '#f59e0b'" 
                                class="w-8 h-8 rounded-full border-2 border-gray-200 hover:scale-110 transition-transform"
                                style="background-color: #f59e0b;"
                                title="Laranja"
                            ></button>
                            <button 
                                type="button" 
                                @click="cor = '#eab308'" 
                                class="w-8 h-8 rounded-full border-2 border-gray-200 hover:scale-110 transition-transform"
                                style="background-color: #eab308;"
                                title="Amarelo"
                            ></button>
                            <button 
                                type="button" 
                                @click="cor = '#06b6d4'" 
                                class="w-8 h-8 rounded-full border-2 border-gray-200 hover:scale-110 transition-transform"
                                style="background-color: #06b6d4;"
                                title="Ciano"
                            ></button>
                            <button 
                                type="button" 
                                @click="cor = '#ec4899'" 
                                class="w-8 h-8 rounded-full border-2 border-gray-200 hover:scale-110 transition-transform"
                                style="background-color: #ec4899;"
                                title="Rosa"
                            ></button>
                            <button 
                                type="button" 
                                @click="cor = '#6366f1'" 
                                class="w-8 h-8 rounded-full border-2 border-gray-200 hover:scale-110 transition-transform"
                                style="background-color: #6366f1;"
                                title="Índigo"
                            ></button>
                            <button 
                                type="button" 
                                @click="cor = '#14b8a6'" 
                                class="w-8 h-8 rounded-full border-2 border-gray-200 hover:scale-110 transition-transform"
                                style="background-color: #14b8a6;"
                                title="Teal"
                            ></button>
                        </div>
                    </div>

                    @if($tag->alunos_count > 0)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-800">
                                <strong>{{ $tag->alunos_count }}</strong> {{ $tag->alunos_count === 1 ? 'aluno está usando' : 'alunos estão usando' }} esta tag.
                            </p>
                        </div>
                    @endif
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <x-button type="button" variant="outline" :href="route('tags.index')">
                        Cancelar
                    </x-button>
                    <x-button type="submit">
                        Atualizar Tag
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>

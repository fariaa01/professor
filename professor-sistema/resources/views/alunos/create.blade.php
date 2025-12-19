@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Novo Aluno</h1>
            <p class="mt-1 text-sm text-gray-600">Preencha os dados do aluno</p>
        </div>

        <x-card>
            <form method="POST" action="{{ route('alunos.store') }}">
                @csrf

                <!-- Informações Básicas -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informações Básicas</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <x-input-label for="nome" value="Nome Completo *" />
                            <x-input id="nome" name="nome" type="text" value="{{ old('nome') }}" required autofocus />
                            <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" value="E-mail" />
                            <x-input id="email" name="email" type="email" value="{{ old('email') }}" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="telefone" value="Telefone" />
                            <x-input id="telefone" name="telefone" type="text" value="{{ old('telefone') }}" placeholder="(00) 00000-0000" />
                            <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="endereco" value="Endereço" />
                            <x-input id="endereco" name="endereco" type="text" value="{{ old('endereco') }}" />
                            <x-input-error :messages="$errors->get('endereco')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 my-6"></div>

                <!-- Responsável (se menor de idade) -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Responsável (opcional)</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="responsavel" value="Nome do Responsável" />
                            <x-input id="responsavel" name="responsavel" type="text" value="{{ old('responsavel') }}" />
                            <x-input-error :messages="$errors->get('responsavel')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="telefone_responsavel" value="Telefone do Responsável" />
                            <x-input id="telefone_responsavel" name="telefone_responsavel" type="text" value="{{ old('telefone_responsavel') }}" placeholder="(00) 00000-0000" />
                            <x-input-error :messages="$errors->get('telefone_responsavel')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 my-6"></div>

                <!-- Informações da Aula -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informações da Aula</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="valor_aula" value="Valor da Aula (R$)" />
                            <x-input id="valor_aula" name="valor_aula" :money="true" value="{{ old('valor_aula') }}" placeholder="0,00" />
                            <x-input-error :messages="$errors->get('valor_aula')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="data_inicio" value="Data de Início" />
                            <x-input id="data_inicio" name="data_inicio" type="date" value="{{ old('data_inicio') }}" />
                            <x-input-error :messages="$errors->get('data_inicio')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 my-6"></div>

                <!-- Observações -->
                <div class="mb-6">
                    <x-input-label for="observacoes" value="Observações" />
                    <x-textarea id="observacoes" name="observacoes" rows="4" placeholder="Informações adicionais sobre o aluno...">{{ old('observacoes') }}</x-textarea>
                    <x-input-error :messages="$errors->get('observacoes')" class="mt-2" />
                </div>

                <div class="border-t border-gray-200 my-6"></div>

                <!-- Tags/Etiquetas -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tags</h3>
                    <p class="text-sm text-gray-600 mb-3">Selecione as etiquetas para categorizar o aluno</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($tags as $tag)
                            <label class="flex items-center p-3 bg-gray-50 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                                <input 
                                    type="checkbox" 
                                    name="tags[]" 
                                    value="{{ $tag->id }}"
                                    {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                >
                                <span class="ml-3 flex items-center gap-2">
                                    <span 
                                        class="inline-block w-3 h-3 rounded-full" 
                                        style="background-color: {{ $tag->cor }};"
                                    ></span>
                                    <span class="text-sm font-medium text-gray-700">{{ $tag->nome }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                </div>

                <div class="border-t border-gray-200 my-6"></div>

                <!-- Horários das Aulas -->
                <div class="mb-6" x-data="horariosManager()">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Horários das Aulas</h3>
                            <p class="mt-1 text-sm text-gray-600">Defina os dias e horários das aulas semanais</p>
                        </div>
                        <x-button type="button" variant="outline" @click="adicionarHorario()">
                            + Adicionar Horário
                        </x-button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(horario, index) in horarios" :key="index">
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Dia da Semana</label>
                                        <select 
                                            x-bind:id="'dia_semana_' + index" 
                                            x-bind:name="'horarios[' + index + '][dia_semana]'"
                                            x-model="horario.dia_semana"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            required>
                                            <option value="">Selecione...</option>
                                            <option value="0">Domingo</option>
                                            <option value="1">Segunda-feira</option>
                                            <option value="2">Terça-feira</option>
                                            <option value="3">Quarta-feira</option>
                                            <option value="4">Quinta-feira</option>
                                            <option value="5">Sexta-feira</option>
                                            <option value="6">Sábado</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Hora Início</label>
                                        <input 
                                            type="time" 
                                            x-bind:id="'hora_inicio_' + index"
                                            x-bind:name="'horarios[' + index + '][hora_inicio]'"
                                            x-model="horario.hora_inicio"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Hora Fim</label>
                                        <input 
                                            type="time" 
                                            x-bind:id="'hora_fim_' + index"
                                            x-bind:name="'horarios[' + index + '][hora_fim]'"
                                            x-model="horario.hora_fim"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            required>
                                    </div>

                                    <div class="flex items-end">
                                        <button 
                                            type="button" 
                                            @click="removerHorario(index)"
                                            class="w-full px-4 py-2 bg-red-50 text-red-600 rounded-md hover:bg-red-100 transition-colors">
                                            Remover
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="horarios.length === 0" class="text-center py-8 text-gray-500">
                            Nenhum horário cadastrado. Clique em "Adicionar Horário" para começar.
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="ativo" value="1" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Aluno ativo</span>
                    </label>
                </div>

                <!-- Botões -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                    <x-button variant="outline" type="button" onclick="window.history.back()">
                        Cancelar
                    </x-button>
                    <x-button variant="primary" type="submit">
                        Cadastrar Aluno
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>

    @push('scripts')
    <script>
        function horariosManager() {
            return {
                horarios: [],
                adicionarHorario() {
                    this.horarios.push({
                        dia_semana: '',
                        hora_inicio: '',
                        hora_fim: ''
                    });
                },
                removerHorario(index) {
                    this.horarios.splice(index, 1);
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Máscara para telefone
            const telefoneInput = document.getElementById('telefone');
            if (telefoneInput) {
                IMask(telefoneInput, {
                    mask: '(00) 00000-0000'
                });
            }

            // Máscara para telefone do responsável
            const telefoneResponsavelInput = document.getElementById('telefone_responsavel');
            if (telefoneResponsavelInput) {
                IMask(telefoneResponsavelInput, {
                    mask: '(00) 00000-0000'
                });
            }

            // Máscara para valor da aula
            const valorAulaInput = document.getElementById('valor_aula');
            if (valorAulaInput) {
                IMask(valorAulaInput, {
                    mask: Number,
                    scale: 2,
                    thousandsSeparator: '.',
                    radix: ',',
                    mapToRadix: ['.'],
                    min: 0,
                    max: 999999
                });
            }
        });
    </script>
    @endpush
</div>
@endsection

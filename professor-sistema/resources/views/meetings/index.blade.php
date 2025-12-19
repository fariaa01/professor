@extends('layouts.app')

@section('content')
<div class="py-8">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Reuniões Online') }}
            </h2>
            <a href="{{ route('meetings.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Nova Reunião
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Tabs -->
                    <div class="mb-6 border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8">
                            <button class="tab-btn active border-blue-600 text-blue-600 py-4 px-1 border-b-2 font-medium text-sm" data-tab="agendadas">
                                Agendadas
                            </button>
                            <button class="tab-btn border-transparent text-gray-500 hover:text-gray-700 py-4 px-1 border-b-2 font-medium text-sm" data-tab="encerradas">
                                Encerradas
                            </button>
                        </nav>
                    </div>

                    <!-- Lista de Reuniões Agendadas -->
                    <div id="agendadas" class="tab-content">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @forelse($meetings->where('status', '!=', 'encerrada') as $meeting)
                                <div class="border rounded-lg p-4 hover:shadow-lg transition">
                                    <div class="flex items-start justify-between mb-3">
                                        <h3 class="font-semibold text-lg">{{ $meeting->title }}</h3>
                                        <span class="px-2 py-1 text-xs rounded-full bg-{{ $meeting->status_color }}-100 text-{{ $meeting->status_color }}-800">
                                            {{ $meeting->status_label }}
                                        </span>
                                    </div>

                                    @if($meeting->aluno)
                                        <p class="text-sm text-gray-600 mb-2">
                                            <strong>Aluno:</strong> {{ $meeting->aluno->nome }}
                                        </p>
                                    @endif

                                    @if($meeting->scheduled_at)
                                        <p class="text-sm text-gray-600 mb-3">
                                            <strong>Agendada:</strong> {{ $meeting->scheduled_at->format('d/m/Y H:i') }}
                                        </p>
                                    @endif

                                    <div class="flex gap-2 mt-4">
                                        <a href="{{ route('meetings.room', $meeting->room_id) }}" 
                                           class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center px-3 py-2 rounded text-sm">
                                            Entrar
                                        </a>
                                        <a href="{{ route('meetings.show', $meeting) }}" 
                                           class="flex-1 bg-gray-600 hover:bg-gray-700 text-white text-center px-3 py-2 rounded text-sm">
                                            Detalhes
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full text-center py-12 text-gray-500">
                                    Nenhuma reunião agendada
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Lista de Reuniões Encerradas -->
                    <div id="encerradas" class="tab-content hidden">
                        <div class="space-y-3">
                            @forelse($meetings->where('status', 'encerrada') as $meeting)
                                <div class="border rounded-lg p-4 hover:bg-gray-50">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="font-semibold">{{ $meeting->title }}</h3>
                                            @if($meeting->aluno)
                                                <p class="text-sm text-gray-600">{{ $meeting->aluno->nome }}</p>
                                            @endif
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $meeting->ended_at->format('d/m/Y H:i') }} • Duração: {{ $meeting->duration_minutes }} min
                                            </p>
                                        </div>
                                        <a href="{{ route('meetings.show', $meeting) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                            Ver detalhes →
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12 text-gray-500">
                                    Nenhuma reunião encerrada
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tabs functionality
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const tabName = btn.dataset.tab;
                
                // Update buttons
                document.querySelectorAll('.tab-btn').forEach(b => {
                    b.classList.remove('active', 'border-blue-600', 'text-blue-600');
                    b.classList.add('border-transparent', 'text-gray-500');
                });
                btn.classList.add('active', 'border-blue-600', 'text-blue-600');
                btn.classList.remove('border-transparent', 'text-gray-500');
                
                // Update content
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                });
                document.getElementById(tabName).classList.remove('hidden');
            });
        });
    </script>
</div>
@endsection

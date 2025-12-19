@extends('layouts.app')

@section('content')
<div class="py-8">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $meeting->title }}
            </h2>
            <span class="px-3 py-1 rounded-full text-sm bg-{{ $meeting->status_color }}-100 text-{{ $meeting->status_color }}-800">
                {{ $meeting->status_label }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Informações da Reunião -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Detalhes</h3>

                            @if($meeting->description)
                                <div class="mb-4">
                                    <label class="text-sm font-medium text-gray-600">Descrição</label>
                                    <p class="mt-1">{{ $meeting->description }}</p>
                                </div>
                            @endif

                            @if($meeting->aluno)
                                <div class="mb-4">
                                    <label class="text-sm font-medium text-gray-600">Aluno</label>
                                    <p class="mt-1">{{ $meeting->aluno->nome }}</p>
                                </div>
                            @endif

                            @if($meeting->scheduled_at)
                                <div class="mb-4">
                                    <label class="text-sm font-medium text-gray-600">Agendamento</label>
                                    <p class="mt-1">{{ $meeting->scheduled_at->format('d/m/Y H:i') }}</p>
                                </div>
                            @endif

                            @if($meeting->started_at)
                                <div class="mb-4">
                                    <label class="text-sm font-medium text-gray-600">Iniciada em</label>
                                    <p class="mt-1">{{ $meeting->started_at->format('d/m/Y H:i') }}</p>
                                </div>
                            @endif

                            @if($meeting->ended_at)
                                <div class="mb-4">
                                    <label class="text-sm font-medium text-gray-600">Encerrada em</label>
                                    <p class="mt-1">{{ $meeting->ended_at->format('d/m/Y H:i') }}</p>
                                </div>
                            @endif

                            @if($meeting->duration_minutes)
                                <div class="mb-4">
                                    <label class="text-sm font-medium text-gray-600">Duração</label>
                                    <p class="mt-1">{{ $meeting->duration_minutes }} minutos</p>
                                </div>
                            @endif

                            <div class="mb-4">
                                <label class="text-sm font-medium text-gray-600">ID da Sala</label>
                                <p class="mt-1 font-mono text-sm">{{ $meeting->room_id }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Histórico do Chat -->
                    @if($meeting->messages->count() > 0)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold mb-4">Histórico do Chat</h3>

                                <div class="space-y-3">
                                    @foreach($meeting->messages as $message)
                                        <div class="p-3 rounded-lg {{ $message->is_system_message ? 'bg-blue-50 text-center text-sm' : 'bg-gray-50' }}">
                                            @if(!$message->is_system_message)
                                                <div class="flex justify-between items-start mb-1">
                                                    <span class="font-semibold text-sm">{{ $message->sender_name }}</span>
                                                    <span class="text-xs text-gray-500">{{ $message->formatted_time }}</span>
                                                </div>
                                            @endif
                                            <div class="{{ $message->is_system_message ? 'text-blue-700' : '' }}">
                                                {{ $message->message }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Ações -->
                <div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Ações</h3>

                            <div class="space-y-3">
                                @if($meeting->status !== 'encerrada' && $meeting->status !== 'cancelada')
                                    <a href="{{ route('meetings.room', $meeting->room_id) }}"
                                       class="block w-full bg-green-600 hover:bg-green-700 text-white text-center px-4 py-2 rounded-lg">
                                        Entrar na Sala
                                    </a>

                                    <form action="{{ route('meetings.cancel', $meeting) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                onclick="return confirm('Deseja cancelar esta reunião?')"
                                                class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                                            Cancelar Reunião
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('meetings.index') }}"
                                   class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-700 text-center px-4 py-2 rounded-lg">
                                    Voltar
                                </a>

                                @if($meeting->status === 'encerrada' || $meeting->status === 'cancelada')
                                    <form action="{{ route('meetings.destroy', $meeting) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Deseja excluir esta reunião?')"
                                                class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                                            Excluir Reunião
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Participantes -->
                    @if($meeting->participants && count($meeting->participants) > 0)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold mb-4">Participantes</h3>

                                <div class="space-y-2">
                                    @foreach($meeting->participants as $participant)
                                        <div class="flex items-center gap-2 p-2 bg-gray-50 rounded">
                                            <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-semibold">
                                                {{ substr($participant['name'], 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-sm">{{ $participant['name'] }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $participant['type'] === 'professor' ? 'Professor' : 'Aluno' }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

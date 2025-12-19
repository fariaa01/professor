<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $meeting->title }} - Reunião Online</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { margin: 0; overflow: hidden; background: #1a1a1a; }
        #meeting-container { height: 100vh; display: flex; flex-direction: column; }
        #videos-container { flex: 1; display: flex; gap: 1rem; padding: 1rem; position: relative; }
        #remote-video-container { flex: 1; background: #000; border-radius: 0.5rem; position: relative; overflow: hidden; }
        #local-video-container { position: absolute; bottom: 1rem; right: 1rem; width: 240px; height: 180px; background: #000; border-radius: 0.5rem; border: 2px solid #fff; overflow: hidden; z-index: 10; }
        video { width: 100%; height: 100%; object-fit: cover; }
        #controls { background: rgba(0,0,0,0.8); padding: 1rem; display: flex; justify-between; align-items: center; }
        .control-btn { background: rgba(255,255,255,0.2); border: none; color: white; padding: 1rem; border-radius: 50%; cursor: pointer; transition: all 0.3s; width: 56px; height: 56px; display: flex; align-items: center; justify-content: center; }
        .control-btn:hover { background: rgba(255,255,255,0.3); transform: scale(1.1); }
        .control-btn.active { background: #10b981; }
        .control-btn.inactive { background: #ef4444; }
        .control-btn.danger { background: #dc2626; }
        .control-btn.danger:hover { background: #b91c1c; }
        #chat-panel { width: 320px; background: #fff; display: flex; flex-direction: column; border-radius: 0.5rem; margin: 1rem 1rem 1rem 0; }
        #chat-messages { flex: 1; overflow-y: auto; padding: 1rem; }
        .message { margin-bottom: 0.75rem; padding: 0.5rem; border-radius: 0.5rem; background: #f3f4f6; }
        .message.system { background: #dbeafe; font-size: 0.875rem; text-align: center; color: #1e40af; }
        .message.mine { background: #dbeafe; margin-left: 2rem; }
        .message.other { background: #f3f4f6; margin-right: 2rem; }
        #chat-input-container { padding: 1rem; border-top: 1px solid #e5e7eb; display: flex; gap: 0.5rem; }
        #chat-input { flex: 1; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; }
        .user-info { position: absolute; bottom: 1rem; left: 1rem; background: rgba(0,0,0,0.7); color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; }
        .connection-status { position: absolute; top: 1rem; right: 1rem; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; }
        .connection-status.connected { background: #10b981; color: white; }
        .connection-status.connecting { background: #f59e0b; color: white; }
        .connection-status.disconnected { background: #ef4444; color: white; }
        #loading { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.9); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; z-index: 9999; }
    </style>
</head>
<body>
    <div id="loading">
        <div class="text-center">
            <div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-white mx-auto mb-4"></div>
            <p>Iniciando reunião...</p>
        </div>
    </div>

    <div id="meeting-container">
        <div id="videos-container">
            <!-- Vídeo Remoto (Participante) -->
            <div id="remote-video-container">
                <div class="connection-status connecting" id="connection-status">Conectando...</div>
                <video id="remote-video" autoplay playsinline></video>
                <div class="user-info" id="remote-user-info">Aguardando participante...</div>
            </div>

            <!-- Vídeo Local (Você) -->
            <div id="local-video-container">
                <video id="local-video" autoplay playsinline muted></video>
                <div class="user-info">Você</div>
            </div>

            <!-- Chat Panel -->
            <div id="chat-panel">
                <div style="padding: 1rem; border-bottom: 1px solid #e5e7eb; font-weight: 600;">
                    Chat da Reunião
                </div>
                <div id="chat-messages"></div>
                <div id="chat-input-container">
                    <input type="text" id="chat-input" placeholder="Digite uma mensagem..." />
                    <button onclick="sendChatMessage()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Enviar
                    </button>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <div id="controls">
            <div class="flex items-center justify-center gap-6">
                <button id="toggle-audio" class="control-btn active" onclick="toggleAudio()" title="Microfone">
                    <svg id="audio-on-icon" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7 4a3 3 0 016 0v4a3 3 0 11-6 0V4zm4 10.93A7.001 7.001 0 0017 8a1 1 0 10-2 0A5 5 0 015 8a1 1 0 00-2 0 7.001 7.001 0 006 6.93V17H6a1 1 0 100 2h8a1 1 0 100-2h-3v-2.07z" clip-rule="evenodd"/>
                    </svg>
                    <svg id="audio-off-icon" class="w-6 h-6 hidden" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                    </svg>
                </button>

                <button id="toggle-video" class="control-btn active" onclick="toggleVideo()" title="Câmera">
                    <svg id="video-on-icon" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                    </svg>
                    <svg id="video-off-icon" class="w-6 h-6 hidden" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                    </svg>
                </button>

                <button id="share-screen" class="control-btn" onclick="toggleScreenShare()" title="Compartilhar Tela">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clip-rule="evenodd"/>
                    </svg>
                </button>

                <div class="text-white text-sm font-medium" id="meeting-timer">00:00</div>

                <!-- Separador com mais espaço antes do botão de desligar -->
                <div class="mx-4"></div>

                <button class="control-btn danger" onclick="endMeeting()" title="Encerrar Reunião">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <script>
        // Configurações da reunião
        const meetingConfig = {
            roomId: '{{ $meeting->room_id }}',
            userId: {{ $userId }},
            userType: '{{ $userType }}',
            meetingId: {{ $meeting->id }},
            userName: '{{ Auth::user()->name ?? ($meeting->aluno->nome ?? "Usuário") }}',
            csrfToken: '{{ csrf_token() }}'
        };

        // Estado da aplicação
        let localStream = null;
        let remoteStream = null;
        let peerConnection = null;
        let isAudioEnabled = true;
        let isVideoEnabled = true;
        let isSharingScreen = false;
        let meetingStartTime = Date.now();
        let timerInterval = null;

        // Configuração ICE servers
        const iceServers = {!! json_encode(config('webrtc.ice_servers')) !!};

        // Inicializa reunião ao carregar página
        document.addEventListener('DOMContentLoaded', initMeeting);

        async function initMeeting() {
            try {
                // Captura mídia local
                localStream = await navigator.mediaDevices.getUserMedia({
                    video: true,
                    audio: true
                });

                document.getElementById('local-video').srcObject = localStream;

                // Conecta ao Echo/WebSocket
                await connectToSignaling();

                // Inicia timer
                startTimer();

                // Remove loading
                document.getElementById('loading').style.display = 'none';

                // Carrega mensagens do chat
                await loadChatMessages();

            } catch (error) {
                console.error('Erro ao inicializar:', error);
                alert('Erro ao acessar câmera e microfone. Verifique as permissões.');
            }
        }

        async function connectToSignaling() {
            // Aqui você conectaria ao Laravel Echo
            // Por enquanto, vamos simular a funcionalidade básica
            console.log('Conectando ao signaling server...');
        }

        function toggleAudio() {
            if (localStream) {
                const audioTrack = localStream.getAudioTracks()[0];
                audioTrack.enabled = !audioTrack.enabled;
                isAudioEnabled = audioTrack.enabled;

                const btn = document.getElementById('toggle-audio');
                const onIcon = document.getElementById('audio-on-icon');
                const offIcon = document.getElementById('audio-off-icon');

                if (isAudioEnabled) {
                    btn.classList.add('active');
                    btn.classList.remove('inactive');
                    onIcon.classList.remove('hidden');
                    offIcon.classList.add('hidden');
                } else {
                    btn.classList.remove('active');
                    btn.classList.add('inactive');
                    onIcon.classList.add('hidden');
                    offIcon.classList.remove('hidden');
                }
            }
        }

        function toggleVideo() {
            if (localStream) {
                const videoTrack = localStream.getVideoTracks()[0];
                videoTrack.enabled = !videoTrack.enabled;
                isVideoEnabled = videoTrack.enabled;

                const btn = document.getElementById('toggle-video');
                const onIcon = document.getElementById('video-on-icon');
                const offIcon = document.getElementById('video-off-icon');

                if (isVideoEnabled) {
                    btn.classList.add('active');
                    btn.classList.remove('inactive');
                    onIcon.classList.remove('hidden');
                    offIcon.classList.add('hidden');
                } else {
                    btn.classList.remove('active');
                    btn.classList.add('inactive');
                    onIcon.classList.add('hidden');
                    offIcon.classList.remove('hidden');
                }
            }
        }

        async function toggleScreenShare() {
            if (!isSharingScreen) {
                try {
                    const screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true });
                    const screenTrack = screenStream.getVideoTracks()[0];

                    if (peerConnection) {
                        const sender = peerConnection.getSenders().find(s => s.track.kind === 'video');
                        if (sender) {
                            sender.replaceTrack(screenTrack);
                        }
                    }

                    screenTrack.onended = () => {
                        toggleScreenShare();
                    };

                    isSharingScreen = true;
                    document.getElementById('share-screen').classList.add('active');
                } catch (error) {
                    console.error('Erro ao compartilhar tela:', error);
                }
            } else {
                const videoTrack = localStream.getVideoTracks()[0];
                if (peerConnection) {
                    const sender = peerConnection.getSenders().find(s => s.track.kind === 'video');
                    if (sender) {
                        sender.replaceTrack(videoTrack);
                    }
                }
                isSharingScreen = false;
                document.getElementById('share-screen').classList.remove('active');
            }
        }

        async function endMeeting() {
            if (confirm('Deseja realmente encerrar a reunião?')) {
                try {
                    // Para streams
                    if (localStream) {
                        localStream.getTracks().forEach(track => track.stop());
                    }

                    // Fecha peer connection
                    if (peerConnection) {
                        peerConnection.close();
                    }

                    // Envia requisição para encerrar
                    const response = await fetch(`/meetings/${meetingConfig.roomId}/end`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': meetingConfig.csrfToken
                        }
                    });

                    const data = await response.json();
                    window.location.href = data.redirect_url || '/meetings';
                } catch (error) {
                    console.error('Erro ao encerrar reunião:', error);
                    window.location.href = '/meetings';
                }
            }
        }

        async function sendChatMessage() {
            const input = document.getElementById('chat-input');
            const message = input.value.trim();

            if (message) {
                try {
                    const response = await fetch(`/meetings/${meetingConfig.roomId}/chat`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': meetingConfig.csrfToken
                        },
                        body: JSON.stringify({
                            message: message,
                            sender_type: meetingConfig.userType,
                            sender_id: meetingConfig.userId
                        })
                    });

                    const data = await response.json();
                    addMessageToChat(data.message, true);
                    input.value = '';
                } catch (error) {
                    console.error('Erro ao enviar mensagem:', error);
                }
            }
        }

        async function loadChatMessages() {
            try {
                const response = await fetch(`/meetings/${meetingConfig.roomId}/chat`);
                const messages = await response.json();
                messages.forEach(msg => addMessageToChat(msg, false));
            } catch (error) {
                console.error('Erro ao carregar mensagens:', error);
            }
        }

        function addMessageToChat(msg, scrollToBottom = true) {
            const container = document.getElementById('chat-messages');
            const div = document.createElement('div');

            if (msg.is_system_message) {
                div.className = 'message system';
                div.textContent = msg.message;
            } else {
                const isMine = msg.sender_id == meetingConfig.userId && msg.sender_type == meetingConfig.userType;
                div.className = `message ${isMine ? 'mine' : 'other'}`;
                div.innerHTML = `
                    <div style="font-weight: 600; font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">
                        ${msg.sender_name} <span style="font-weight: 400;">${msg.formatted_time}</span>
                    </div>
                    <div>${msg.message}</div>
                `;
            }

            container.appendChild(div);

            if (scrollToBottom) {
                container.scrollTop = container.scrollHeight;
            }
        }

        function startTimer() {
            timerInterval = setInterval(() => {
                const elapsed = Date.now() - meetingStartTime;
                const minutes = Math.floor(elapsed / 60000);
                const seconds = Math.floor((elapsed % 60000) / 1000);
                document.getElementById('meeting-timer').textContent =
                    `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }, 1000);
        }

        // Enter para enviar mensagem
        document.getElementById('chat-input').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendChatMessage();
            }
        });

        // Cleanup ao fechar página
        window.addEventListener('beforeunload', () => {
            if (localStream) {
                localStream.getTracks().forEach(track => track.stop());
            }
            if (timerInterval) {
                clearInterval(timerInterval);
            }
        });
    </script>
</body>
</html>

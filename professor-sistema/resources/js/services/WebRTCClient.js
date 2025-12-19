/**
 * WebRTC Client Service
 * Gerencia conexões peer-to-peer para chamadas de vídeo
 */

export class WebRTCClient {
    constructor(roomId, userId, userType, onRemoteStream, onConnectionStateChange) {
        this.roomId = roomId;
        this.userId = userId;
        this.userType = userType;
        this.onRemoteStream = onRemoteStream;
        this.onConnectionStateChange = onConnectionStateChange;
        
        this.peerConnection = null;
        this.localStream = null;
        this.remoteStream = null;
        
        // Configuração dos servidores ICE (STUN/TURN)
        this.config = {
            iceServers: [
                { urls: 'stun:stun.l.google.com:19302' },
                { urls: 'stun:stun1.l.google.com:19302' },
                { urls: 'stun:stun2.l.google.com:19302' },
            ],
            iceTransportPolicy: 'all',
            bundlePolicy: 'max-bundle',
            rtcpMuxPolicy: 'require',
        };
    }

    /**
     * Inicializa a conexão WebRTC
     */
    async initialize() {
        try {
            // Captura mídia local (câmera e microfone)
            this.localStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    width: { min: 640, ideal: 1280, max: 1920 },
                    height: { min: 480, ideal: 720, max: 1080 },
                    frameRate: { ideal: 30, max: 60 },
                    facingMode: 'user',
                },
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true,
                    autoGainControl: true,
                },
            });

            // Cria peer connection
            this.createPeerConnection();

            return this.localStream;
        } catch (error) {
            console.error('Erro ao inicializar WebRTC:', error);
            throw new Error('Não foi possível acessar câmera e microfone. Verifique as permissões.');
        }
    }

    /**
     * Cria a RTCPeerConnection
     */
    createPeerConnection() {
        this.peerConnection = new RTCPeerConnection(this.config);

        // Adiciona tracks locais à conexão
        if (this.localStream) {
            this.localStream.getTracks().forEach(track => {
                this.peerConnection.addTrack(track, this.localStream);
            });
        }

        // Escuta por tracks remotos
        this.peerConnection.ontrack = (event) => {
            console.log('Track remoto recebido:', event.track.kind);
            if (!this.remoteStream) {
                this.remoteStream = new MediaStream();
                this.onRemoteStream(this.remoteStream);
            }
            this.remoteStream.addTrack(event.track);
        };

        // Monitora mudanças no estado da conexão
        this.peerConnection.onconnectionstatechange = () => {
            console.log('Estado da conexão:', this.peerConnection.connectionState);
            if (this.onConnectionStateChange) {
                this.onConnectionStateChange(this.peerConnection.connectionState);
            }
        };

        // Escuta candidatos ICE (serão enviados via signaling)
        this.peerConnection.onicecandidate = (event) => {
            if (event.candidate) {
                console.log('Novo candidato ICE:', event.candidate);
                this.onIceCandidate(event.candidate);
            }
        };

        // Monitora mudanças no estado do ICE
        this.peerConnection.oniceconnectionstatechange = () => {
            console.log('Estado ICE:', this.peerConnection.iceConnectionState);
        };
    }

    /**
     * Cria e envia uma oferta WebRTC
     */
    async createOffer() {
        try {
            const offer = await this.peerConnection.createOffer();
            await this.peerConnection.setLocalDescription(offer);
            return offer;
        } catch (error) {
            console.error('Erro ao criar oferta:', error);
            throw error;
        }
    }

    /**
     * Cria e envia uma resposta WebRTC
     */
    async createAnswer() {
        try {
            const answer = await this.peerConnection.createAnswer();
            await this.peerConnection.setLocalDescription(answer);
            return answer;
        } catch (error) {
            console.error('Erro ao criar resposta:', error);
            throw error;
        }
    }

    /**
     * Processa oferta recebida
     */
    async handleOffer(offer) {
        try {
            await this.peerConnection.setRemoteDescription(new RTCSessionDescription(offer));
            const answer = await this.createAnswer();
            return answer;
        } catch (error) {
            console.error('Erro ao processar oferta:', error);
            throw error;
        }
    }

    /**
     * Processa resposta recebida
     */
    async handleAnswer(answer) {
        try {
            await this.peerConnection.setRemoteDescription(new RTCSessionDescription(answer));
        } catch (error) {
            console.error('Erro ao processar resposta:', error);
            throw error;
        }
    }

    /**
     * Adiciona candidato ICE recebido
     */
    async addIceCandidate(candidate) {
        try {
            await this.peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
        } catch (error) {
            console.error('Erro ao adicionar candidato ICE:', error);
        }
    }

    /**
     * Callback para quando um candidato ICE é gerado (deve ser sobrescrito)
     */
    onIceCandidate(candidate) {
        // Será sobrescrito para enviar via signaling server
        console.warn('onIceCandidate não implementado');
    }

    /**
     * Alterna áudio
     */
    toggleAudio() {
        if (this.localStream) {
            const audioTrack = this.localStream.getAudioTracks()[0];
            if (audioTrack) {
                audioTrack.enabled = !audioTrack.enabled;
                return audioTrack.enabled;
            }
        }
        return false;
    }

    /**
     * Alterna vídeo
     */
    toggleVideo() {
        if (this.localStream) {
            const videoTrack = this.localStream.getVideoTracks()[0];
            if (videoTrack) {
                videoTrack.enabled = !videoTrack.enabled;
                return videoTrack.enabled;
            }
        }
        return false;
    }

    /**
     * Inicia compartilhamento de tela
     */
    async startScreenShare() {
        try {
            const screenStream = await navigator.mediaDevices.getDisplayMedia({
                video: {
                    cursor: 'always',
                    displaySurface: 'monitor',
                },
                audio: false,
            });

            const videoTrack = screenStream.getVideoTracks()[0];
            
            // Substitui o track de vídeo atual
            const sender = this.peerConnection
                .getSenders()
                .find(s => s.track && s.track.kind === 'video');
            
            if (sender) {
                sender.replaceTrack(videoTrack);
            }

            // Quando parar de compartilhar, volta para câmera
            videoTrack.onended = () => {
                this.stopScreenShare();
            };

            return screenStream;
        } catch (error) {
            console.error('Erro ao compartilhar tela:', error);
            throw error;
        }
    }

    /**
     * Para compartilhamento de tela
     */
    async stopScreenShare() {
        if (this.localStream) {
            const videoTrack = this.localStream.getVideoTracks()[0];
            const sender = this.peerConnection
                .getSenders()
                .find(s => s.track && s.track.kind === 'video');
            
            if (sender && videoTrack) {
                await sender.replaceTrack(videoTrack);
            }
        }
    }

    /**
     * Encerra a conexão
     */
    close() {
        // Para todos os tracks
        if (this.localStream) {
            this.localStream.getTracks().forEach(track => track.stop());
        }
        
        if (this.remoteStream) {
            this.remoteStream.getTracks().forEach(track => track.stop());
        }

        // Fecha peer connection
        if (this.peerConnection) {
            this.peerConnection.close();
        }

        this.localStream = null;
        this.remoteStream = null;
        this.peerConnection = null;
    }
}

export default WebRTCClient;

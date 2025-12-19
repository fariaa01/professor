/**
 * Media Service
 * Gerencia captura e controle de mídia (áudio/vídeo)
 */

export class MediaService {
    constructor() {
        this.localStream = null;
        this.screenStream = null;
        this.isAudioEnabled = true;
        this.isVideoEnabled = true;
        this.isSharingScreen = false;
    }

    /**
     * Captura mídia do usuário (câmera e microfone)
     */
    async getUserMedia(constraints = null) {
        try {
            const defaultConstraints = {
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
            };

            this.localStream = await navigator.mediaDevices.getUserMedia(
                constraints || defaultConstraints
            );

            return this.localStream;
        } catch (error) {
            console.error('Erro ao capturar mídia:', error);
            throw this.handleMediaError(error);
        }
    }

    /**
     * Inicia compartilhamento de tela
     */
    async startScreenShare() {
        try {
            this.screenStream = await navigator.mediaDevices.getDisplayMedia({
                video: {
                    cursor: 'always',
                    displaySurface: 'monitor',
                },
                audio: false,
            });

            const screenTrack = this.screenStream.getVideoTracks()[0];
            
            // Detecta quando usuário para de compartilhar
            screenTrack.onended = () => {
                this.stopScreenShare();
            };

            this.isSharingScreen = true;
            return this.screenStream;
        } catch (error) {
            console.error('Erro ao compartilhar tela:', error);
            throw error;
        }
    }

    /**
     * Para compartilhamento de tela
     */
    stopScreenShare() {
        if (this.screenStream) {
            this.screenStream.getTracks().forEach(track => track.stop());
            this.screenStream = null;
        }
        this.isSharingScreen = false;
    }

    /**
     * Liga/Desliga microfone
     */
    toggleAudio() {
        if (this.localStream) {
            const audioTrack = this.localStream.getAudioTracks()[0];
            if (audioTrack) {
                audioTrack.enabled = !audioTrack.enabled;
                this.isAudioEnabled = audioTrack.enabled;
                return this.isAudioEnabled;
            }
        }
        return false;
    }

    /**
     * Liga/Desliga câmera
     */
    toggleVideo() {
        if (this.localStream) {
            const videoTrack = this.localStream.getVideoTracks()[0];
            if (videoTrack) {
                videoTrack.enabled = !videoTrack.enabled;
                this.isVideoEnabled = videoTrack.enabled;
                return this.isVideoEnabled;
            }
        }
        return false;
    }

    /**
     * Troca entre câmera frontal e traseira (mobile)
     */
    async switchCamera() {
        if (this.localStream) {
            const videoTrack = this.localStream.getVideoTracks()[0];
            if (videoTrack) {
                const currentFacingMode = videoTrack.getSettings().facingMode;
                const newFacingMode = currentFacingMode === 'user' ? 'environment' : 'user';
                
                videoTrack.stop();
                
                const newStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: newFacingMode },
                    audio: true,
                });
                
                return newStream;
            }
        }
        return null;
    }

    /**
     * Para todos os streams
     */
    stopAllStreams() {
        if (this.localStream) {
            this.localStream.getTracks().forEach(track => track.stop());
            this.localStream = null;
        }
        
        if (this.screenStream) {
            this.screenStream.getTracks().forEach(track => track.stop());
            this.screenStream = null;
        }

        this.isAudioEnabled = true;
        this.isVideoEnabled = true;
        this.isSharingScreen = false;
    }

    /**
     * Verifica se o navegador suporta getUserMedia
     */
    static isSupported() {
        return !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
    }

    /**
     * Verifica se o navegador suporta compartilhamento de tela
     */
    static isScreenShareSupported() {
        return !!(navigator.mediaDevices && navigator.mediaDevices.getDisplayMedia);
    }

    /**
     * Trata erros de mídia
     */
    handleMediaError(error) {
        let message = 'Erro ao acessar mídia';
        
        switch (error.name) {
            case 'NotAllowedError':
            case 'PermissionDeniedError':
                message = 'Permissão negada para acessar câmera e microfone. Por favor, permita o acesso nas configurações do navegador.';
                break;
            case 'NotFoundError':
            case 'DevicesNotFoundError':
                message = 'Nenhuma câmera ou microfone foi encontrado. Verifique se os dispositivos estão conectados.';
                break;
            case 'NotReadableError':
            case 'TrackStartError':
                message = 'Não foi possível acessar a câmera ou microfone. Verifique se outro aplicativo não está usando.';
                break;
            case 'OverconstrainedError':
            case 'ConstraintNotSatisfiedError':
                message = 'As configurações de mídia solicitadas não podem ser atendidas.';
                break;
            case 'TypeError':
                message = 'Configuração de mídia inválida.';
                break;
            case 'AbortError':
                message = 'Acesso à mídia foi abortado.';
                break;
            default:
                message = `Erro desconhecido: ${error.message}`;
        }
        
        return new Error(message);
    }

    /**
     * Obtém dispositivos disponíveis
     */
    static async getDevices() {
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            return {
                cameras: devices.filter(d => d.kind === 'videoinput'),
                microphones: devices.filter(d => d.kind === 'audioinput'),
                speakers: devices.filter(d => d.kind === 'audiooutput'),
            };
        } catch (error) {
            console.error('Erro ao listar dispositivos:', error);
            return { cameras: [], microphones: [], speakers: [] };
        }
    }
}

export default MediaService;

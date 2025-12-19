/**
 * Signaling Service
 * Gerencia comunicação WebSocket para sinalização WebRTC
 */

import axios from 'axios';

export class SignalingService {
    constructor(roomId, userId, userType, echo) {
        this.roomId = roomId;
        this.userId = userId;
        this.userType = userType;
        this.echo = echo; // Laravel Echo instance
        this.channel = null;
        this.callbacks = {
            onUserJoined: null,
            onUserLeft: null,
            onOffer: null,
            onAnswer: null,
            onIceCandidate: null,
            onMeetingEnded: null,
            onMessage: null,
        };
    }

    /**
     * Conecta ao canal WebSocket da reunião
     */
    connect() {
        this.channel = this.echo.private(`meeting.${this.roomId}`);

        // Escuta quando outro usuário entra
        this.channel.listen('MeetingJoined', (event) => {
            console.log('Usuário entrou:', event);
            if (this.callbacks.onUserJoined) {
                this.callbacks.onUserJoined(event);
            }
        });

        // Escuta quando outro usuário sai
        this.channel.listen('MeetingLeft', (event) => {
            console.log('Usuário saiu:', event);
            if (this.callbacks.onUserLeft) {
                this.callbacks.onUserLeft(event);
            }
        });

        // Escuta ofertas WebRTC
        this.channel.listen('WebRTCOffer', (event) => {
            console.log('Oferta WebRTC recebida:', event);
            if (this.callbacks.onOffer) {
                this.callbacks.onOffer(event.offer, event.from);
            }
        });

        // Escuta respostas WebRTC
        this.channel.listen('WebRTCAnswer', (event) => {
            console.log('Resposta WebRTC recebida:', event);
            if (this.callbacks.onAnswer) {
                this.callbacks.onAnswer(event.answer, event.from);
            }
        });

        // Escuta candidatos ICE
        this.channel.listen('WebRTCIceCandidate', (event) => {
            console.log('Candidato ICE recebido:', event);
            if (this.callbacks.onIceCandidate) {
                this.callbacks.onIceCandidate(event.candidate, event.from);
            }
        });

        // Escuta fim da reunião
        this.channel.listen('MeetingEnded', (event) => {
            console.log('Reunião encerrada:', event);
            if (this.callbacks.onMeetingEnded) {
                this.callbacks.onMeetingEnded(event);
            }
        });

        // Escuta mensagens do chat
        this.channel.listen('MeetingMessageSent', (event) => {
            console.log('Mensagem recebida:', event);
            if (this.callbacks.onMessage) {
                this.callbacks.onMessage(event);
            }
        });

        console.log(`Conectado ao canal meeting.${this.roomId}`);
    }

    /**
     * Registra callbacks
     */
    on(event, callback) {
        if (this.callbacks.hasOwnProperty(`on${this.capitalize(event)}`)) {
            this.callbacks[`on${this.capitalize(event)}`] = callback;
        }
    }

    /**
     * Envia notificação de entrada na sala
     */
    async sendJoin(userName) {
        try {
            await axios.post(`/meetings/${this.roomId}/join`, {
                user_id: this.userId,
                user_name: userName,
                user_type: this.userType,
            });
        } catch (error) {
            console.error('Erro ao enviar join:', error);
        }
    }

    /**
     * Envia notificação de saída da sala
     */
    async sendLeave(userName) {
        try {
            await axios.post(`/meetings/${this.roomId}/leave`, {
                user_id: this.userId,
                user_name: userName,
                user_type: this.userType,
            });
        } catch (error) {
            console.error('Erro ao enviar leave:', error);
        }
    }

    /**
     * Envia oferta WebRTC
     */
    async sendOffer(offer) {
        try {
            await axios.post(`/meetings/${this.roomId}/signal/offer`, {
                offer: offer,
                from: { id: this.userId, type: this.userType },
            });
        } catch (error) {
            console.error('Erro ao enviar oferta:', error);
        }
    }

    /**
     * Envia resposta WebRTC
     */
    async sendAnswer(answer) {
        try {
            await axios.post(`/meetings/${this.roomId}/signal/answer`, {
                answer: answer,
                from: { id: this.userId, type: this.userType },
            });
        } catch (error) {
            console.error('Erro ao enviar resposta:', error);
        }
    }

    /**
     * Envia candidato ICE
     */
    async sendIceCandidate(candidate) {
        try {
            await axios.post(`/meetings/${this.roomId}/signal/ice-candidate`, {
                candidate: candidate,
                from: { id: this.userId, type: this.userType },
            });
        } catch (error) {
            console.error('Erro ao enviar candidato ICE:', error);
        }
    }

    /**
     * Envia mensagem no chat
     */
    async sendMessage(message) {
        try {
            const response = await axios.post(`/meetings/${this.roomId}/chat`, {
                message: message,
                sender_type: this.userType,
                sender_id: this.userId,
            });
            return response.data;
        } catch (error) {
            console.error('Erro ao enviar mensagem:', error);
            throw error;
        }
    }

    /**
     * Carrega mensagens do chat
     */
    async loadMessages() {
        try {
            const response = await axios.get(`/meetings/${this.roomId}/chat`);
            return response.data;
        } catch (error) {
            console.error('Erro ao carregar mensagens:', error);
            return [];
        }
    }

    /**
     * Encerra reunião
     */
    async endMeeting() {
        try {
            const response = await axios.post(`/meetings/${this.roomId}/end`);
            return response.data;
        } catch (error) {
            console.error('Erro ao encerrar reunião:', error);
            throw error;
        }
    }

    /**
     * Desconecta do canal
     */
    disconnect() {
        if (this.channel) {
            this.echo.leave(`meeting.${this.roomId}`);
            this.channel = null;
        }
    }

    /**
     * Helper para capitalizar primeira letra
     */
    capitalize(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
}

export default SignalingService;

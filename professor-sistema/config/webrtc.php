<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Servidores ICE (STUN/TURN)
    |--------------------------------------------------------------------------
    |
    | Configuração dos servidores STUN e TURN para WebRTC.
    | 
    | STUN: Ajuda a descobrir o endereço IP público do cliente
    | TURN: Relay server para quando a conexão P2P direta falha
    |
    */

    'ice_servers' => [
        // Servidores STUN públicos do Google
        [
            'urls' => 'stun:stun.l.google.com:19302',
        ],
        [
            'urls' => 'stun:stun1.l.google.com:19302',
        ],
        [
            'urls' => 'stun:stun2.l.google.com:19302',
        ],
        
        // Exemplo de servidor TURN (descomentar e configurar para produção)
        // [
        //     'urls' => 'turn:your-turn-server.com:3478',
        //     'username' => env('TURN_USERNAME', 'username'),
        //     'credential' => env('TURN_PASSWORD', 'password'),
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Mídia
    |--------------------------------------------------------------------------
    |
    | Configurações padrão para captura de áudio e vídeo
    |
    */

    'media_constraints' => [
        'audio' => [
            'echoCancellation' => true,
            'noiseSuppression' => true,
            'autoGainControl' => true,
        ],
        'video' => [
            'width' => ['min' => 640, 'ideal' => 1280, 'max' => 1920],
            'height' => ['min' => 480, 'ideal' => 720, 'max' => 1080],
            'frameRate' => ['ideal' => 30, 'max' => 60],
            'facingMode' => 'user',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Screen Share
    |--------------------------------------------------------------------------
    |
    | Configurações para compartilhamento de tela
    |
    */

    'screen_share_constraints' => [
        'video' => [
            'cursor' => 'always',
            'displaySurface' => 'monitor',
        ],
        'audio' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Peer Connection
    |--------------------------------------------------------------------------
    |
    | Configurações da RTCPeerConnection
    |
    */

    'peer_connection_config' => [
        'iceTransportPolicy' => 'all', // 'all' ou 'relay'
        'bundlePolicy' => 'max-bundle',
        'rtcpMuxPolicy' => 'require',
    ],

    /*
    |--------------------------------------------------------------------------
    | Limites e Timeouts
    |--------------------------------------------------------------------------
    */

    'max_meeting_duration' => env('MAX_MEETING_DURATION', 240), // minutos
    'connection_timeout' => env('CONNECTION_TIMEOUT', 30), // segundos
    'reconnection_attempts' => env('RECONNECTION_ATTEMPTS', 3),

];

# 🎥 Guia de Reuniões Online - Sistema Professor

## Como Iniciar uma Reunião

### 1️⃣ Professor Cria a Reunião

1. **Acesse o menu "Reuniões"** na navegação principal
2. **Clique em "Nova Reunião"** (botão verde no canto superior direito)
3. **Preencha os dados:**
   - Título (ex: "Aula de Matemática - João Silva")
   - Descrição (opcional)
   - Aluno (selecione o aluno na lista)
   - Data/Hora agendada (opcional)
   - Vincular a uma aula existente (opcional)

4. **Clique em "Criar Reunião"**

### 2️⃣ Iniciando a Chamada

Depois de criar a reunião, você tem 3 opções:

#### Opção A - Iniciar Imediatamente
1. Na lista de reuniões, clique em **"Entrar na Sala"** (botão azul)
2. O navegador pedirá permissão para câmera e microfone - **clique em "Permitir"**
3. Você entrará na sala de espera até o aluno entrar

#### Opção B - Iniciar de uma Aula Agendada
1. Vá em **Aulas > Ver Aula**
2. Se a aula tiver uma reunião vinculada, verá um botão **"Iniciar Reunião"**
3. Clique no botão e será direcionado para a sala

#### Opção C - Enviar Link para o Aluno
1. Na lista de reuniões, copie o **Room ID** da reunião
2. Compartilhe com o aluno via WhatsApp/Email
3. O aluno acessa: `https://seu-site.com/meetings/room/ROOM_ID_AQUI`

### 3️⃣ Aluno Entra na Reunião

**Portal do Aluno:**
1. Aluno faz login no portal (`/aluno/login`)
2. No dashboard, verá as **"Reuniões Agendadas"**
3. Clica em **"Entrar"** quando a reunião estiver disponível
4. Permite câmera e microfone
5. Entra na sala automaticamente

**OU via Link Direto:**
1. Aluno acessa o link enviado pelo professor
2. Faz login (se necessário)
3. Permite câmera/microfone
4. Entra na sala

### 4️⃣ Durante a Reunião

**Controles Disponíveis:**

- 🎤 **Microfone** - Ligar/Desligar (botão vermelho quando desligado)
- 📹 **Câmera** - Ligar/Desligar (botão vermelho quando desligado)
- 🖥️ **Compartilhar Tela** - Compartilha sua tela com o aluno
- 💬 **Chat** - Abrir/Fechar painel de chat lateral
- 📞 **Sair** - Encerra sua participação na reunião

**Layout da Tela:**
- Vídeo do participante remoto: **Tela grande principal**
- Seu próprio vídeo: **Card pequeno no canto inferior direito**
- Chat: **Painel lateral direito (pode abrir/fechar)**
- Controles: **Barra inferior fixa**

### 5️⃣ Encerrando a Reunião

**Professor:**
1. Clique em **"Encerrar Reunião"** (botão vermelho)
2. Confirme o encerramento
3. Será redirecionado para o relatório da aula (se houver vínculo)
4. A duração será calculada automaticamente

**Aluno:**
1. Clique em **"Sair"**
2. Será redirecionado para "Minhas Aulas"

---

## 🔧 Configuração Técnica

### Pré-requisitos

1. **Laravel Echo e Broadcasting configurado:**
```bash
npm install --save laravel-echo pusher-js
# OU
npm install --save socket.io-client
```

2. **Configure o `.env`:**
```env
BROADCAST_DRIVER=pusher
# OU
BROADCAST_DRIVER=redis

# Para Pusher
PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-app-key
PUSHER_APP_SECRET=your-app-secret
PUSHER_APP_CLUSTER=mt1

# Servidores STUN/TURN (configurados em config/webrtc.php)
```

3. **Execute as migrations:**
```bash
php artisan migrate
```

4. **Compile os assets:**
```bash
npm run dev
# OU para produção
npm run build
```

### Testando Localmente

Para testar em desenvolvimento, você precisa de **2 navegadores diferentes** ou **modo anônimo + normal**:

1. **Navegador 1** - Entre como Professor
   - Acesse: `http://localhost/meetings`
   - Crie e entre na reunião

2. **Navegador 2** - Entre como Aluno
   - Acesse: `http://localhost/aluno/login`
   - Entre na mesma reunião

### Servidores STUN/TURN

**Configuração Padrão (Públicos - Grátis):**
```javascript
// Já configurado em config/webrtc.php
stun:stun.l.google.com:19302
stun:stun1.l.google.com:19302
```

**Para Produção (Recomendado):**
Use um serviço gerenciado como:
- [Twilio TURN](https://www.twilio.com/stun-turn)
- [Xirsys](https://xirsys.com/)
- [Metered](https://www.metered.ca/stun-turn)

Configure em `config/webrtc.php`.

---

## 🎯 Fluxo Completo Resumido

```
PROFESSOR                           SISTEMA                          ALUNO
    |                                  |                               |
    |--1. Cria reunião---------------->|                               |
    |                                  |--2. Gera Room ID              |
    |<-3. Reunião criada---------------|                               |
    |                                  |                               |
    |--4. Entra na sala--------------->|                               |
    |<-5. WebSocket conectado----------|                               |
    |<-6. Solicita câmera/mic----------|                               |
    |                                  |                               |
    |                                  |<--7. Aluno entra na sala------|
    |                                  |--8. WebSocket conectado------>|
    |                                  |--9. Solicita câmera/mic------>|
    |                                  |                               |
    |<-10. WebRTC Offer via WS---------|--11. WebRTC Offer------------>|
    |                                  |<-12. WebRTC Answer------------|
    |<-13. ICE Candidates--------------|--14. ICE Candidates---------->|
    |                                  |                               |
    |========== CONEXÃO PEER-TO-PEER ESTABELECIDA ====================>|
    |                                  |                               |
    |--Áudio/Vídeo-------------------- DIRETO ------------------------>|
    |<-Áudio/Vídeo-------------------- DIRETO -------------------------|
    |                                  |                               |
    |--Chat message------------------>|--Broadcast chat-------------->|
    |<-Chat message-------------------|<-Chat message-----------------|
    |                                  |                               |
    |--15. Encerra reunião------------>|                               |
    |                                  |--16. Notifica aluno---------->|
    |                                  |--17. Calcula duração          |
    |<-18. Redireciona p/ relatório----|                               |
    |                                  |--19. Redireciona p/ aulas---->|
```

---

## 🐛 Solução de Problemas

### Câmera/Microfone não funciona
- Verifique as permissões do navegador (ícone de cadeado na barra de endereço)
- Teste em: chrome://settings/content/camera e chrome://settings/content/microphone
- Use HTTPS em produção (obrigatório para WebRTC)

### Vídeo não conecta
- Verifique se o WebSocket está conectado (console do navegador)
- Teste se o Broadcasting está funcionando: `php artisan queue:work`
- Verifique configuração STUN/TURN

### Chat não envia mensagens
- Verifique console do navegador por erros
- Confirme que Laravel Echo está conectado
- Teste o broadcasting: `php artisan tinker` → `broadcast(new App\Events\MeetingMessageSent(...));`

### Áudio com eco
- Use fones de ouvido
- Ative cancelamento de eco nas configurações do navegador

---

## 📱 Acesso Rápido

- **Professor - Reuniões:** `http://localhost/meetings`
- **Professor - Criar:** `http://localhost/meetings/create`
- **Aluno - Login:** `http://localhost/aluno/login`
- **Sala de Reunião:** `http://localhost/meetings/room/{room_id}`

---

## 🎓 Recursos Avançados (Futuro)

- ✅ Gravação de aulas
- ✅ Quadro branco colaborativo
- ✅ Múltiplos participantes (grupo)
- ✅ Transcrição automática
- ✅ Compartilhamento de arquivos
- ✅ Enquetes e quizzes em tempo real

---

**Desenvolvido para Professores Particulares 🎯**

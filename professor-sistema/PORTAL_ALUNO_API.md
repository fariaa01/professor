# 📱 Portal do Aluno - Documentação da API

Base URL: `http://localhost:8000/api/aluno`

## 🔐 Autenticação

### 1. Login
**Endpoint:** `POST /api/aluno/login`

**Request:**
```json
{
  "email": "maria@exemplo.com",
  "password": "mariae"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Login realizado com sucesso!",
  "data": {
    "aluno": {
      "id": 1,
      "nome": "Maria Santos",
      "email": "maria@exemplo.com",
      "telefone": "(11) 98765-4321",
      "professor_id": 1
    },
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "bearer",
    "expires_in": 3600
  }
}
```

**Response (401) - Credenciais inválidas:**
```json
{
  "success": false,
  "message": "Credenciais inválidas. Verifique seu email e senha."
}
```

**Response (403) - Conta inativa:**
```json
{
  "success": false,
  "message": "Sua conta está inativa. Entre em contato com seu professor."
}
```

---

### 2. Dados do Aluno Autenticado
**Endpoint:** `GET /api/aluno/me`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "nome": "Maria Santos",
    "email": "maria@exemplo.com",
    "telefone": "(11) 98765-4321",
    "endereco": "Rua Exemplo, 123",
    "responsavel": "João Santos",
    "telefone_responsavel": "(11) 99999-8888",
    "data_inicio": "01/09/2025",
    "ativo": true
  }
}
```

---

### 3. Logout
**Endpoint:** `POST /api/aluno/logout`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Logout realizado com sucesso!"
}
```

---

### 4. Refresh Token
**Endpoint:** `POST /api/aluno/refresh`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "bearer",
    "expires_in": 3600
  }
}
```

---

### 5. Alterar Senha
**Endpoint:** `POST /api/aluno/change-password`

**Headers:**
```
Authorization: Bearer {token}
```

**Request:**
```json
{
  "current_password": "mariae",
  "new_password": "novaSenha123",
  "new_password_confirmation": "novaSenha123"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Senha alterada com sucesso!"
}
```

**Response (400) - Senha atual incorreta:**
```json
{
  "success": false,
  "message": "Senha atual incorreta."
}
```

---

## 📊 Dashboard

### 6. Dashboard Completo
**Endpoint:** `GET /api/aluno/dashboard`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "aluno": {
      "nome": "Maria Santos",
      "email": "maria@exemplo.com"
    },
    "estatisticas": {
      "total_aulas": 45,
      "aulas_realizadas": 38,
      "faltas": 3,
      "carga_horaria_minutos": 2280,
      "carga_horaria_horas": 38.0
    },
    "proximas_aulas": [
      {
        "id": 102,
        "data_hora": "12/12/2025 14:00",
        "data_hora_iso": "2025-12-12T14:00:00-03:00",
        "duracao_minutos": 60,
        "status": "agendada"
      }
    ],
    "aulas_recentes": [
      {
        "id": 98,
        "data_hora": "10/12/2025 14:00",
        "duracao_minutos": 60,
        "conteudo": "Revisão de Matemática - Funções quadráticas e gráficos...",
        "tem_materiais": true,
        "tem_exercicios": true
      }
    ],
    "plano": {
      "tipo_plano": "pacote",
      "tipo_plano_nome": "Pacote de Aulas",
      "valor_aula": null,
      "valor_total": 840.00,
      "quantidade_aulas": 12,
      "data_inicio": "01/12/2025",
      "data_fim": "01/03/2026",
      "proximas_parcelas": [
        {
          "numero_parcela": 2,
          "total_parcelas": 3,
          "valor": 280.00,
          "data_vencimento": "10/01/2026",
          "status_pagamento": "pendente"
        }
      ]
    }
  }
}
```

---

## 📚 Aulas

### 7. Listar Todas as Aulas
**Endpoint:** `GET /api/aluno/aulas`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 98,
      "data_hora": "10/12/2025 14:00",
      "duracao_minutos": 60,
      "status": "realizada",
      "conteudo_resumo": "Revisão de Matemática - Funções quadráticas...",
      "tem_materiais": true,
      "tem_exercicios": true
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 20,
    "total": 45
  }
}
```

---

### 8. Detalhes de uma Aula
**Endpoint:** `GET /api/aluno/aulas/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 98,
    "data_hora": "10/12/2025 14:00",
    "data_hora_iso": "2025-12-10T14:00:00-03:00",
    "duracao_minutos": 60,
    "status": "realizada",
    "conteudo": "Trabalhamos funções quadráticas, gráficos e análise de parábolas. Revisamos conceitos de vértice, raízes e concavidade.",
    "materiais": "Apostila capítulo 5, videoaula YouTube (link), exercícios resolvidos PDF",
    "exercicios": "Lista 10 - Questões 15 a 25 sobre funções. Resolver para próxima aula.",
    "dificuldades": "Dificuldade em identificar o vértice algebricamente. Precisa revisar fórmula.",
    "pontos_atencao": "Atenção especial em problemas contextualizados (física, geometria).",
    "observacoes": "Aula produtiva. Evoluindo bem no conteúdo."
  }
}
```

**Response (404) - Aula não encontrada:**
```json
{
  "success": false,
  "message": "Aula não encontrada."
}
```

---

## 💰 Pagamentos

### 9. Informações do Plano
**Endpoint:** `GET /api/aluno/pagamentos/plano`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 2,
    "tipo_plano": "pacote",
    "tipo_plano_nome": "Pacote de Aulas",
    "valor_aula": null,
    "valor_total": 840.00,
    "quantidade_aulas": 12,
    "data_inicio": "01/12/2025",
    "data_fim": "01/03/2026",
    "observacoes": "Pacote promocional fim de ano"
  }
}
```

---

### 10. Listar Todas as Parcelas
**Endpoint:** `GET /api/aluno/pagamentos/parcelas`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 4,
      "numero_parcela": 1,
      "total_parcelas": 3,
      "parcela_formatada": "1/3",
      "valor": 280.00,
      "data_vencimento": "10/12/2025",
      "data_pagamento": "09/12/2025",
      "status_pagamento": "pago",
      "forma_pagamento": "pix"
    },
    {
      "id": 5,
      "numero_parcela": 2,
      "total_parcelas": 3,
      "parcela_formatada": "2/3",
      "valor": 280.00,
      "data_vencimento": "10/01/2026",
      "data_pagamento": null,
      "status_pagamento": "pendente",
      "forma_pagamento": null
    }
  ]
}
```

---

### 11. Resumo Financeiro
**Endpoint:** `GET /api/aluno/pagamentos/resumo`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "total_plano": 840.00,
    "total_pago": 280.00,
    "total_pendente": 280.00,
    "total_atrasado": 280.00,
    "parcelas_pagas": 1,
    "parcelas_pendentes": 2
  }
}
```

---

## 🔒 Segurança e Isolamento

- **Token JWT:** Todos os endpoints (exceto login) exigem `Authorization: Bearer {token}`
- **Isolamento por professor:** Aluno só acessa dados do próprio professor (`user_id`)
- **Isolamento por aluno:** Cada aluno só vê suas próprias aulas, plano e parcelas
- **Validação automática:** Middleware valida `aluno_id` e `professor_id` em todas as requests

---

## 📝 Senhas Padrão dos Alunos Existentes

| Aluno | Email | Senha Padrão |
|-------|-------|--------------|
| Maria Santos | maria@exemplo.com | `mariae` |
| Pedro Costa | pedro@exemplo.com | `pedroe` |
| Ana Silva | ana@exemplo.com | `anaexe` |
| Lucas Oliveira | lucas@exemplo.com | `lucase` |
| Carla Souza | carla@exemplo.com | `carlae` |

**Recomendação:** Alunos devem alterar a senha após primeiro login usando `/api/aluno/change-password`

---

## 🧪 Testando a API

### Exemplo com cURL:

```bash
# Login
curl -X POST http://localhost:8000/api/aluno/login \
  -H "Content-Type: application/json" \
  -d '{"email":"maria@exemplo.com","password":"mariae"}'

# Dashboard (substitua {TOKEN} pelo token recebido no login)
curl -X GET http://localhost:8000/api/aluno/dashboard \
  -H "Authorization: Bearer {TOKEN}"
```

### Exemplo com JavaScript (Fetch):

```javascript
// Login
const login = await fetch('http://localhost:8000/api/aluno/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    email: 'maria@exemplo.com',
    password: 'mariae'
  })
});

const { data } = await login.json();
const token = data.access_token;

// Dashboard
const dashboard = await fetch('http://localhost:8000/api/aluno/dashboard', {
  headers: { 'Authorization': `Bearer ${token}` }
});

const dashboardData = await dashboard.json();
console.log(dashboardData);
```

---

## ✅ Status da Implementação

- ✅ Autenticação JWT completa
- ✅ Login por email/senha
- ✅ Isolamento de dados por aluno e professor
- ✅ Dashboard com estatísticas
- ✅ Listagem e detalhes de aulas
- ✅ Informações de plano e parcelas
- ✅ Alteração de senha
- ✅ Senhas geradas automaticamente no cadastro
- ✅ API totalmente funcional e segura

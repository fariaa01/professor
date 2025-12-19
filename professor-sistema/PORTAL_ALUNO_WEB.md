# Portal do Aluno - Interface Web

## 📱 Acesso ao Portal

**URL:** `http://localhost:8000/aluno/login`

O portal do aluno é uma interface web completa onde os alunos podem:
- Visualizar suas aulas (próximas e recentes)
- Acompanhar seu plano contratado
- Ver suas parcelas e pagamentos
- Consultar estatísticas de presença e desempenho

---

## 🔐 Credenciais de Teste

### Alunos Cadastrados no Sistema

| Nome | Email | Senha |
|------|-------|-------|
| Maria Santos | maria@exemplo.com | `mariae` |
| Pedro Costa | pedro@exemplo.com | `pedroe` |
| Ana Silva | ana@exemplo.com | `anaexe` |
| Lucas Oliveira | lucas@exemplo.com | `lucase` |
| Carla Souza | carla@exemplo.com | `carlae` |

> **Nota:** As senhas são geradas automaticamente baseadas no email do aluno (primeiros 6 caracteres sem símbolos).

---

## 🎨 Interface e Visual

O portal do aluno foi desenvolvido com **exatamente o mesmo visual** das páginas do professor:
- **Framework CSS:** Tailwind CSS
- **Interatividade:** Alpine.js
- **Design:** Cards, badges e layout responsivo
- **Cores:** Paleta azul/indigo com gradientes

### Páginas Disponíveis

#### 1. Login (`/aluno/login`)
- Formulário de login com email e senha
- Botões rápidos para teste com credenciais pré-preenchidas
- Fundo com gradiente azul
- Validação de campos obrigatórios

#### 2. Dashboard (`/aluno/dashboard`)
- **Estatísticas:**
  - Total de aulas
  - Aulas realizadas
  - Faltas
  - Carga horária total
  
- **Próximas Aulas:**
  - Lista das próximas 5 aulas agendadas
  - Data, horário e duração
  - Status visual com badges

- **Aulas Recentes:**
  - Últimas aulas realizadas
  - Conteúdo estudado
  - Indicadores de materiais e exercícios

- **Plano Contratado:**
  - Tipo de plano (Por Aula / Pacote / Mensalidade)
  - Valor total e quantidade de aulas
  - Período de vigência
  
- **Próximas Parcelas:**
  - Lista de cobranças futuras
  - Status: Pago / Pendente / Atrasado
  - Valores e datas de vencimento
  - Destaque em vermelho para parcelas atrasadas

---

## 🔧 Arquitetura Técnica

### Frontend
- **Autenticação:** JWT Token armazenado em `localStorage`
- **API Base URL:** `http://localhost:8000/api/aluno`
- **Comunicação:** Fetch API com headers de autenticação

### Fluxo de Login
```javascript
1. Usuário preenche email e senha
2. Requisição POST para /api/aluno/login
3. API retorna JWT token + dados do aluno
4. Token salvo em localStorage
5. Redirecionamento para dashboard
6. Todas as requisições incluem header Authorization: Bearer {token}
```

### Segurança
- Token JWT expira em 60 minutos
- Payload do token inclui `aluno_id` e `professor_id`
- Todas as queries filtram por `aluno_id` e `professor_id` automaticamente
- Logout limpa o localStorage e redireciona para login

---

## 📊 Dados do Dashboard

### Estatísticas Calculadas
```php
- total_aulas: COUNT de todas as aulas do aluno
- aulas_realizadas: COUNT de aulas com status 'realizada'
- faltas: COUNT de aulas com status 'falta'
- carga_horaria_horas: SUM(duracao_minutos) / 60
```

### Próximas Aulas
- Ordenadas por `data_hora ASC`
- Apenas aulas com status diferente de 'realizada' e 'falta'
- Limitadas a 5 registros

### Aulas Recentes
- Ordenadas por `data_hora DESC`
- Apenas aulas com status 'realizada'
- Limitadas a 5 registros
- Inclui flags: `tem_materiais`, `tem_exercicios`

### Plano Atual
- Busca o plano ativo (`ativo = 1`) mais recente
- Eager loading de parcelas relacionadas
- Próximas parcelas: Ordenadas por `data_vencimento ASC`, limitadas a 5

---

## 🎨 Componentes Visuais

### Cards de Estatísticas
```html
<div class="bg-white rounded-lg shadow p-6">
    <p class="text-sm text-gray-600 mb-1">Título</p>
    <p class="text-3xl font-bold text-{color}-600">Valor</p>
</div>
```

### Badges de Status
- **Agendada:** Azul claro (`bg-blue-100 text-blue-800`)
- **Pago:** Verde (`bg-green-100 text-green-800`)
- **Pendente:** Amarelo (`bg-yellow-100 text-yellow-800`)
- **Atrasado:** Vermelho (`bg-red-100 text-red-800`)

### Cards de Aulas
```html
<div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
    <!-- Conteúdo da aula -->
</div>
```

### Chips de Recursos
```html
<span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded">
    Materiais
</span>
```

---

## 🔄 Endpoints da API Utilizados

### Autenticação
- `POST /api/aluno/login` - Login com email/senha
- `POST /api/aluno/logout` - Logout (invalida token)
- `POST /api/aluno/refresh` - Renovar token

### Dashboard
- `GET /api/aluno/dashboard` - Dados completos do dashboard

### Aulas
- `GET /api/aluno/aulas` - Listar todas as aulas
- `GET /api/aluno/aulas/{id}` - Detalhes de uma aula

### Pagamentos
- `GET /api/aluno/pagamentos/plano` - Plano atual
- `GET /api/aluno/pagamentos/parcelas` - Todas as parcelas
- `GET /api/aluno/pagamentos/resumo` - Resumo financeiro

---

## 🚀 Como Testar

### 1. Acessar Login
```
http://localhost:8000/aluno/login
```

### 2. Fazer Login
- Usar credenciais de teste (ex: maria@exemplo.com / mariae)
- OU clicar em um dos botões de teste rápido

### 3. Explorar Dashboard
- Ver estatísticas gerais
- Verificar próximas aulas
- Consultar aulas recentes
- Analisar plano e parcelas

### 4. Testar Logout
- Clicar no botão "Sair" no canto superior direito
- Verificar redirecionamento para login

---

## 🎯 Funcionalidades Futuras

### Páginas Adicionais
- [ ] `/aluno/aulas` - Lista completa de aulas com filtros
- [ ] `/aluno/aulas/{id}` - Detalhes da aula individual
- [ ] `/aluno/pagamentos` - Histórico completo de pagamentos
- [ ] `/aluno/perfil` - Edição de dados pessoais e senha

### Melhorias
- [ ] Notificações de novas aulas
- [ ] Download de materiais didáticos
- [ ] Chat com o professor
- [ ] Calendário interativo de aulas
- [ ] Gráficos de evolução

---

## 📝 Notas de Desenvolvimento

### Separação de Contextos
- **Professor:** Autenticação via sessão web (`Auth::guard('web')`)
- **Aluno:** Autenticação via JWT API (`Auth::guard('aluno')`)
- Ambos usam o mesmo banco de dados mas são completamente isolados

### Geração de Senhas
Quando um novo aluno é cadastrado pelo professor:
```php
$senha = preg_replace('/[^a-z0-9]/i', '', substr($email, 0, 6));
$senha = strlen($senha) >= 4 ? $senha : '123456';
$aluno->password = Hash::make($senha);
```

### Estrutura de Arquivos
```
app/Http/Controllers/Aluno/
├── AuthController.php       (API JWT)
├── DashboardController.php  (API Dashboard)
├── AulaController.php       (API Aulas)
├── PagamentoController.php  (API Pagamentos)
└── WebAuthController.php    (Web Views)

resources/views/aluno/
├── login.blade.php          (Formulário de login)
└── dashboard.blade.php      (Dashboard principal)

resources/views/layouts/
└── aluno.blade.php          (Layout base)
```

---

## ✅ Checklist de Validação

- [x] Login funcional com credenciais de teste
- [x] Dashboard carrega dados via API
- [x] Estatísticas exibidas corretamente
- [x] Próximas aulas listadas
- [x] Aulas recentes visíveis
- [x] Plano contratado exibido
- [x] Parcelas ordenadas por vencimento
- [x] Parcelas atrasadas destacadas em vermelho
- [x] Logout funcional
- [x] Visual idêntico ao portal do professor
- [x] Responsivo para mobile
- [x] Token JWT renovável
- [x] Isolamento de dados por professor

---

## 📚 Documentação Relacionada

- `PORTAL_ALUNO_API.md` - Documentação completa da API
- `README.md` - Documentação geral do sistema

---

**Desenvolvido com Laravel 12 + Tailwind CSS + Alpine.js + JWT**

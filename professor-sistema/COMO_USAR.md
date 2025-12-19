# Professor System - Como Usar

## 🚀 Sistema pronto para uso!

### Iniciando o servidor

Para iniciar o sistema, execute no terminal (dentro da pasta `professor-sistema`):

```bash
php artisan serve
```

O sistema estará disponível em: **http://127.0.0.1:8000**

---

## 📝 Como Cadastrar e Usar

### 1. **Primeira vez - Criar sua conta**

1. Acesse: http://127.0.0.1:8000
2. Você será redirecionado para a tela de **Login**
3. Clique em **"Cadastre-se agora"** (link azul no final do formulário)
4. Preencha seus dados:
   - **Nome Completo**: Seu nome como professor
   - **E-mail**: Seu email (será usado para login)
   - **Senha**: Mínimo 8 caracteres
   - **Confirmar Senha**: Digite a mesma senha
5. Clique em **"Criar Conta"**
6. Você será automaticamente logado no sistema

### 2. **Login após cadastro**

1. Acesse: http://127.0.0.1:8000/login
2. Digite seu **e-mail** e **senha**
3. Clique em **"Entrar"**

---

## 🎯 Usando o Dashboard

Após fazer login, você verá o **Dashboard** com:

### **Cards de Estatísticas** (topo da página):
- 📊 **Alunos Ativos**: Total de alunos cadastrados
- ✅ **Aulas Realizadas**: Quantidade de aulas do mês
- ⚠️ **Faltas (Aluno)**: Aulas canceladas pelos alunos
- ⏱️ **Carga Horária**: Total de horas trabalhadas no mês

### **Calendário da Semana** (esquerda):
- Visualização das aulas da semana atual
- Cada dia mostra:
  - Nome do aluno
  - Horário da aula
  - Duração
  - Status (Agendada, Realizada, Cancelada)
- O dia atual aparece destacado em azul

### **Próximas Aulas** (direita):
- Lista das aulas agendadas para os próximos 7 dias
- Informações detalhadas de cada aula
- Observações (se houver)

### **Ações Rápidas** (inferior direita):
- 📝 **Nova Aula**: Agendar uma nova aula
- 👥 **Novo Aluno**: Cadastrar um novo aluno
- 📊 **Ver Relatórios**: Acessar relatórios gerenciais

---

## 👤 Usuário de Teste (já criado)

Se você executou os seeders, já existe um usuário de exemplo:

- **E-mail**: `professor@exemplo.com`
- **Senha**: `password`

Este usuário já tem:
- ✅ 5 alunos cadastrados
- ✅ Várias aulas de exemplo
- ✅ Dados da semana atual e próxima

---

## 🎨 Design

O sistema utiliza:
- **Estilo shadcn**: Design moderno e limpo
- **Tailwind CSS**: Estilos responsivos
- **Cores profissionais**: Azul e cinza predominantes
- **Ícones SVG**: Interface visual intuitiva
- **Responsivo**: Funciona em desktop e celular

---

## 🔧 Estrutura do Projeto

```
📁 professor-sistema/
├── app/
│   ├── Models/
│   │   ├── User.php (Professor)
│   │   ├── Aluno.php
│   │   └── Aula.php
│   └── Http/Controllers/
│       └── DashboardController.php
├── resources/
│   └── views/
│       ├── dashboard.blade.php (Página principal)
│       ├── auth/ (Login e Registro)
│       ├── components/ (Componentes reutilizáveis)
│       └── layouts/ (Layouts base)
└── database/
    ├── migrations/ (Estrutura do banco)
    └── seeders/ (Dados de exemplo)
```

---

## 📚 Funcionalidades Principais

### ✅ Implementado:
- Sistema de autenticação (Login/Registro)
- Dashboard com visão geral
- Calendário semanal de aulas
- Estatísticas mensais
- Cards informativos
- Design moderno estilo shadcn
- Navbar responsiva
- Componentes reutilizáveis

### 🔄 Próximas funcionalidades (a implementar):
- CRUD de Alunos
- CRUD de Aulas
- Calendário mensal completo
- Relatórios detalhados
- Filtros e buscas
- Notificações
- Exportação de dados

---

## 🆘 Comandos Úteis

### Recriar banco de dados com dados de exemplo:
```bash
php artisan migrate:fresh --seed
```

### Recompilar assets (CSS/JS):
```bash
npm run build
```

### Limpar cache:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## 📞 Suporte

Sistema desenvolvido com Laravel 12, Tailwind CSS e design inspirado em shadcn/ui.

**Boa gestão de aulas! 🎓**

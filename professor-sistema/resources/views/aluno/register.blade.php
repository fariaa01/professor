<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Cadastro Aluno</title>
</head>
<body>
    <h1>Cadastro de Aluno</h1>
    @if($errors->any())
        <div style="color:red">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('aluno.register.post') }}">
        @csrf
        <div>
            <label>Nome</label>
            <input name="name" required />
        </div>
        <div>
            <label>E-mail</label>
            <input name="email" type="email" required />
        </div>
        <div>
            <label>Senha</label>
            <input name="password" type="password" required />
        </div>
        <div>
            <label>Confirme a senha</label>
            <input name="password_confirmation" type="password" required />
        </div>
        <button type="submit">Criar Conta</button>
    </form>
</body>
</html>

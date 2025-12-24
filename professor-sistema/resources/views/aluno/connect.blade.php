<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Conectar com Professor</title>
</head>
<body>
    <h1>Conectar com um Professor</h1>
    @if(session('status'))
        <div style="color:green">{{ session('status') }}</div>
    @endif
    @if($errors->any())
        <div style="color:red">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('aluno.connect.post') }}">
        @csrf
        <div>
            <label>ID do professor</label>
            <input name="professor_id" required />
        </div>
        <button type="submit">Conectar</button>
    </form>
</body>
</html>

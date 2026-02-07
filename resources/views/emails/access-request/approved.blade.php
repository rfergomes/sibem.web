<!DOCTYPE html>
<html>

<head>
    <title>Acesso Aprovado - SIBEM</title>
</head>

<body style="font-family: Arial, sans-serif; color: #333;">
    <h2>Bem-vindo ao SIBEM!</h2>
    <p>Olá, {{ $user->name }}.</p>
    <p>Sua solicitação de acesso ao Sistema de Inventário de Bens Móveis foi aprovada.</p>

    <p>Seguem suas credenciais de acesso:</p>
    <ul>
        <li><strong>E-mail:</strong> {{ $user->email }}</li>
        <li><strong>Senha Provisória:</strong> {{ $password }}</li>
    </ul>

    <p>Recomendamos que você altere sua senha após o primeiro acesso.</p>

    <p><a href="{{ route('login') }}">Clique aqui para acessar o sistema</a></p>
</body>

</html>
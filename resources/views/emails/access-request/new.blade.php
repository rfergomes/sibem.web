<!DOCTYPE html>
<html>

<head>
    <title>Nova Solicitação de Acesso</title>
</head>

<body style="font-family: Arial, sans-serif; color: #333;">
    <h2>Nova Solicitação de Acesso - SIBEM</h2>
    <p>Uma nova solicitação de acesso foi recebida.</p>

    <ul>
        <li><strong>Nome:</strong> {{ $solicitacao->nome }}</li>
        <li><strong>Email:</strong> {{ $solicitacao->email }}</li>
        <li><strong>Telefone:</strong> {{ $solicitacao->telefone }}</li>
        <li><strong>Cidade:</strong> {{ $solicitacao->cidade }}</li>
        <li><strong>Regional:</strong> {{ $solicitacao->regional->nome ?? 'Não informada' }}</li>
        <li><strong>Observações:</strong> {{ $solicitacao->observacoes }}</li>
    </ul>

    <p>Acesse o painel administrativo para aprovar ou rejeitar esta solicitação.</p>
</body>

</html>
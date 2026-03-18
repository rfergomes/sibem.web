<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recebemos sua mensagem - SIBEM</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .wrapper {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .header {
            background-color: #1a5276;
            padding: 40px 32px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 24px;
            letter-spacing: 0.5px;
        }
        .body {
            padding: 40px 32px;
            line-height: 1.6;
        }
        .body h2 {
            color: #1a5276;
            font-size: 20px;
            margin-top: 0;
        }
        .body p {
            font-size: 16px;
            color: #555;
        }
        .footer {
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            padding: 25px 32px;
            text-align: center;
            font-size: 13px;
            color: #95a5a6;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #1a5276;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>SIBEM</h1>
        </div>

        <div class="body">
            <h2>Olá, {{ $contact->name }}!</h2>
            <p>Recebemos sua mensagem enviada através do nosso formulário de contato.</p>
            <p>Agradecemos o seu interesse no <strong>SIBEM (Sistema para Inventário de Bens Móveis)</strong>. Nossa equipe analisará sua solicitação e entrará em contato com você o mais breve possível no endereço <strong>{{ $contact->email }}</strong>.</p>
            
            <p>Enquanto isso, você pode conferir nossa documentação ou novidades em nosso portal.</p>
            
            <a href="{{ config('app.url') }}" class="btn">Visitar nosso portal</a>

            <p style="margin-top: 30px;">Atenciosamente,<br>Equipe SIBEM</p>
        </div>

        <div class="footer">
            <p>Este é um e-mail automático, por favor não responda.</p>
            <p>&copy; {{ date('Y') }} SIBEM - Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>

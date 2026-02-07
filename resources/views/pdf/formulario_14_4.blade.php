<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }

        .header {
            text-align: center;
            border: 2px solid #000;
            padding: 10px;
            margin-bottom: 20px;
        }

        .content {
            margin: 20px 0;
            line-height: 1.6;
            text-align: justify;
        }

        .signature {
            margin-top: 50px;
            text-align: center;
            border-top: 1px solid #000;
            width: 60%;
            margin-left: 20%;
            padding-top: 5px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h3>{{ $titulo }}</h3>
        <div>Modelo 14.4</div>
    </div>

    <div class="content">
        <p>{{ $content }}</p>
        <p><strong>Bem:</strong> {{ $bem }}</p>
        <p><strong>Data:</strong> {{ $data }}</p>
    </div>

    <div class="signature">
        Responsável pela Retirada
    </div>
</body>

</html>
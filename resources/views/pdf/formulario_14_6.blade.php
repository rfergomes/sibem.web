<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .box {
            border: 1px solid #000;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <h2 style="text-align: center;">{{ $titulo }}</h2>
    <div class="box">
        <h3>Dados Anteriores</h3>
        <p>{{ $bem_antigo }}</p>
    </div>
    <div class="box">
        <h3>Dados Atuais</h3>
        <p>{{ $bem_novo }}</p>
    </div>
    <div class="box">
        <h3>Justificativa</h3>
        <p>{{ $motivo }}</p>
    </div>
    <p style="text-align: center;">Data: {{ $data }}</p>
</body>

</html>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .move-box {
            border: 1px solid #000;
            padding: 20px;
            text-align: center;
            font-size: 14pt;
            margin: 20px 0;
        }

        .arrow {
            font-size: 20pt;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>{{ $titulo }}</h2>
        <p>Data: {{ $data }}</p>
    </div>

    <p><strong>Bem:</strong> {{ $bem->descricao }} (ID: {{ $bem->id_bem }})</p>

    <div class="move-box">
        {{ $origem }}
        <br><br>
        <span class="arrow">&darr;</span>
        <br><br>
        {{ $destino }}
    </div>

    <p style="text-align: center; margin-top: 50px;">____________________________________<br>Responsável</p>
</body>

</html>
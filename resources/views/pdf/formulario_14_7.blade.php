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

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <tr>
            <th style="border: 1px solid #000; padding: 5px; width: 40%;">Administração / Localidade</th>
            <th style="border: 1px solid #000; padding: 5px; width: 20%;">Código SIGA</th>
            <th style="border: 1px solid #000; padding: 5px; width: 40%;">Cidade - UF</th>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px;">
                {{ $administracao }}<br><small>{{ $razao_social ?? '' }}</small></td>
            <td style="border: 1px solid #000; padding: 5px;">{{ $cod_siga ?? 'N/A' }}</td>
            <td style="border: 1px solid #000; padding: 5px;">{{ $cidade }} - {{ $uf ?? '' }}</td>
        </tr>
        <tr>
            <th style="border: 1px solid #000; padding: 5px;">CNPJ</th>
            <th style="border: 1px solid #000; padding: 5px;">Endereço</th>
            <th style="border: 1px solid #000; padding: 5px;">Setor</th>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px;">{{ $cnpj ?? '' }}</td>
            <td style="border: 1px solid #000; padding: 5px;">{{ $logradouro ?? '' }}, {{ $numero ?? '' }}</td>
            <td style="border: 1px solid #000; padding: 5px;">{{ $setor }}</td>
        </tr>
    </table>

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
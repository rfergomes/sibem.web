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
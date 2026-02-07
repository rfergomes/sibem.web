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
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>{{ $titulo }}</h2>
        <p>Inventário Reference: {{ $inventario->codigo_unico }}</p>
        <p>Data: {{ $data_emissao }}</p>
    </div>

    <h3>Resumo Estatístico</h3>
    <table>
        <tr>
            <th>Total de Itens</th>
            <td>{{ $stats['totalItems'] }}</td>
        </tr>
        <tr>
            <th>Itens Encontrados</th>
            <td>{{ $stats['found'] }}</td>
        </tr>
        <tr>
            <th>Itens Não Encontrados</th>
            <td>{{ $stats['missing'] }}</td>
        </tr>
        <tr>
            <th>Novos Itens (Divergências)</th>
            <td>{{ $stats['new'] }}</td>
        </tr>
    </table>

    <h3>Assinaturas</h3>
    <br><br><br>
    <div style="border-top: 1px solid #000; width: 40%; display: inline-block; margin-right: 10%;">
        {{ $inventario->responsavel }}<br>Responsável</div>
    <div style="border-top: 1px solid #000; width: 40%; display: inline-block;">Comissão de Inventário</div>
</body>

</html>
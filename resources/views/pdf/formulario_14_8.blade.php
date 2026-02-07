<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #eee;
        }
    </style>
</head>

<body>
    <h2>{{ $titulo }} - {{ $mes }}/{{ $ano }}</h2>
    <p>Data de Emissão: {{ $data }}</p>

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

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Descrição</th>
                <th>Tipo Movimento</th>
                <th>Observação</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->id_bem }}</td>
                    <td>{{ $item->bem->descricao ?? 'N/A' }}</td>
                    <td>{{ $item->status_leitura == 'novo_sistema' ? 'Entrada (Novo)' : 'Movimentação' }}</td>
                    <td>{{ $item->observacao }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Nenhuma movimentação registrada neste período.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
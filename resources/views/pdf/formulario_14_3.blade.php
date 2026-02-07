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

        .title {
            font-size: 14pt;
            font-weight: bold;
            margin: 5px 0;
        }

        .section {
            margin-bottom: 15px;
            border: 1px solid #000;
            padding: 10px;
        }

        .label {
            font-weight: bold;
        }

        .value {
            border-bottom: 1px dotted #000;
            min-height: 20px;
            display: inline-block;
            width: 80%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
        }

        .signatures {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .sign-box {
            border-top: 1px solid #000;
            width: 40%;
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="title">CONGREGAÇÃO CRISTÃ NO BRASIL</div>
        <div>{{ $titulo }} (Modelo SIGA {{ $form_code }})</div>
    </div>

    <div class="section">
        <h3>Identificação</h3>
        <p><span class="label">Administração:</span> {{ $administracao }}</p>
        <p><span class="label">Localidade/Setor:</span> {{ $setor }}</p>
        <p><span class="label">Data de Emissão:</span> {{ $data_emissao }}</p>
    </div>

    <div class="section">
        <h3>Dados do Bem</h3>
        <p><span class="label">Descrição:</span> {{ $descricao_bem }}</p>
        <p><span class="label">Motivo da Saída:</span> {{ $motivo }}</p>
    </div>

    <div class="section">
        <h3>Termo de Responsabilidade</h3>
        <p>Declaro para os devidos fins que o bem acima descrito foi baixado do inventário desta localidade pelo motivo
            supracitado, estando em conformidade com as normas administrativas.</p>
    </div>

    <div class="signatures">
        <center>
            <br><br><br>
            <div style="width: 200px; border-top: 1px solid #000;">{{ $responsavel }}<br>Responsável</div>
            <br><br>
            <div style="width: 200px; border-top: 1px solid #000;">Administração<br>Visto</div>
        </center>
    </div>
</body>

</html>
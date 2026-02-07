<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Formulário 14.1 - Declaração de Doação de Bem Móvel</title>
    <style>
        @page {
            margin: 20mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            border: 2px solid #000;
            padding: 10px;
            margin-bottom: 15px;
        }

        .header h1 {
            margin: 5px 0;
            font-size: 16pt;
        }

        .header .info {
            display: inline-block;
            margin: 0 10px;
            font-size: 9pt;
        }

        .section-title {
            background: #000;
            color: #fff;
            padding: 5px;
            font-weight: bold;
            margin-top: 15px;
        }

        .field-group {
            margin: 10px 0;
        }

        .field-label {
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 3px;
        }

        .field-value {
            border-bottom: 1px solid #000;
            min-height: 20px;
            padding: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th,
        td {
            padding: 5px;
            text-align: left;
        }

        .checkbox {
            display: inline-block;
            width: 15px;
            height: 15px;
            border: 1px solid #000;
            margin-right: 5px;
            vertical-align: middle;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 40px;
            padding-top: 5px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <div style="float: left; width: 15%;">
            <strong>CCB</strong>
        </div>
        <div style="float: left; width: 70%; text-align: center;">
            <h1>MANUAL ADMINISTRATIVO</h1>
            <div><strong>PATRIMÔNIO – BENS MÓVEIS</strong></div>
        </div>
        <div style="float: right; width: 15%; text-align: right; font-size: 8pt;">
            <div>SEÇÃO: 14</div>
            <div>FL./FLS: 21/46</div>
            <div>DATA REVISÃO: 24/09/2019</div>
            <div>EDIÇÃO: 6</div>
            <div>REVISÃO: 1</div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="margin: 5px 0;">FORMULÁRIO 14.1</h2>
        <h3 style="margin: 5px 0;">DECLARAÇÃO DE DOAÇÃO DE BENS MÓVEIS</h3>
        <div style="text-align: right; font-size: 9pt; margin-top: 10px;">
            Data Emissão: {{ $dataEmissao }}
        </div>
    </div>

    <div class="section-title">A - LOCALIDADE RECEBEDORA</div>
    <table>
        <tr>
            <th style="width: 50%;">Administração</th>
            <th style="width: 30%;">Cidade</th>
            <th style="width: 20%;">Setor</th>
        </tr>
        <tr>
            <td>{{ $administracao }}</td>
            <td>{{ $cidade }}</td>
            <td>{{ $setor }}</td>
        </tr>
    </table>

    <div class="section-title">B - DESCRIÇÃO DO BEM</div>
    <div class="field-group">
        <div class="field-value" style="min-height: 40px;">{{ $descricaoBem }}</div>
    </div>

    <table style="margin-top: 10px;">
        <tr>
            <th style="width: 25%;">Nº Nota Fiscal</th>
            <th style="width: 25%;">Data de emissão</th>
            <th style="width: 25%;">Valor</th>
            <th style="width: 25%;">Fornecedor</th>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
    </table>

    <div style="margin: 15px 0; padding: 10px; border: 1px solid #000;">
        <p style="margin: 5px 0;"><strong>Declaramos que estamos doando à CONGREGAÇÃO CRISTÃ NO BRASIL</strong> o bem
            acima descrito, de nossa propriedade, livre e desembaraçado de dívidas e ônus, para uso da Casa de Oração
            acima identificada.</p>

        <div style="margin: 10px 0;">
            <div><span class="checkbox"></span> O bem tem mais de cinco anos de uso e o documento fiscal de aquisição
                está anexo.</div>
            <div><span class="checkbox"></span> O bem tem mais de cinco anos de uso, porém o documento fiscal de
                aquisição foi extraviado.</div>
            <div><span class="checkbox"></span> O bem tem até cinco anos de uso e o documento fiscal de aquisição está
                anexo.</div>
        </div>

        <p style="margin: 5px 0; font-size: 9pt;"><em>Por ser verdade firmamos esta declaração.</em></p>
    </div>

    <div class="field-group">
        <div class="field-label">Local e data:</div>
        <div class="field-value">{{ $localData }}</div>
    </div>

    <div class="section-title">C - DOADOR</div>
    <table>
        <tr>
            <th colspan="2">Dados do doador</th>
            <th>Dados do cônjuge</th>
        </tr>
        <tr>
            <td style="width: 40%;"><strong>Nome</strong><br>&nbsp;<br>&nbsp;</td>
            <td style="width: 30%;"><strong>Endereço</strong><br>&nbsp;<br>&nbsp;</td>
            <td style="width: 30%;"><strong>CPF</strong><br>&nbsp;<br>&nbsp;</td>
        </tr>
        <tr>
            <td><strong>RG</strong><br>&nbsp;</td>
            <td colspan="2"><strong>Assinatura</strong><br>&nbsp;<br>&nbsp;</td>
        </tr>
    </table>

    <div class="section-title">D - TERMO DE ACEITE DA DOAÇÃO</div>
    <div style="margin: 10px 0; padding: 10px; border: 1px solid #000;">
        <p style="margin: 5px 0;">A Congregação Cristã No Brasil aceita a presente doação por atender necessidade do
            momento.</p>
    </div>

    <table style="margin-top: 10px;">
        <tr>
            <th style="width: 33%;">Nome</th>
            <th style="width: 33%;">Assinatura</th>
            <th style="width: 34%;"></th>
        </tr>
        <tr>
            <td><strong>Administrador Assessor</strong><br>&nbsp;<br>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><strong>Doador</strong><br>&nbsp;<br>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
    </table>

    <div style="text-align: center; margin-top: 20px; font-size: 8pt;">
        sp.saopaulo.manualadm@congregacao.org.br
    </div>
</body>

</html>
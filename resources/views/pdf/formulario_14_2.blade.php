<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Formulário 14.2 - Ocorrência de Entrada de Bens Móveis</title>
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

        .section-title {
            background: #000;
            color: #fff;
            padding: 5px;
            font-weight: bold;
            margin-top: 15px;
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

        .checked {
            background: #000;
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
            <div>REVISÃO: 1</div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="margin: 5px 0;">FORMULÁRIO 14.2</h2>
        <h3 style="margin: 5px 0;">OCORRÊNCIA DE ENTRADA DE BENS MÓVEIS</h3>
        <div style="text-align: right; font-size: 9pt; margin-top: 10px;">
            Data Emissão: {{ $dataEmissao }}
        </div>
    </div>

    <div class="section-title">A - LOCALIDADE RECEBEDORA</div>
    <table>
        <tr>
            <th style="width: 40%;">Administração / Localidade</th>
            <th style="width: 20%;">Código SIGA</th>
            <th style="width: 40%;">Cidade - UF</th>
        </tr>
        <tr>
            <td>{{ $administracao }}<br><small>{{ $razao_social ?? '' }}</small></td>
            <td>{{ $cod_siga ?? 'N/A' }}</td>
            <td>{{ $cidade }} - {{ $uf ?? '' }}</td>
        </tr>
        <tr>
            <th>CNPJ</th>
            <th>Endereço</th>
            <th>Setor</th>
        </tr>
        <tr>
            <td>{{ $cnpj ?? '' }}</td>
            <td>{{ $logradouro ?? '' }}, {{ $numero ?? '' }}</td>
            <td>{{ $setor }}</td>
        </tr>
    </table>

    <div class="section-title">B - MOTIVO DA OCORRÊNCIA</div>
    <div style="margin: 10px 0;">
        <table style="border: none;">
            <tr style="border: none;">
                <td style="border: none; width: 33%;">
                    <span class="checkbox"></span> Entrada
                </td>
                <td style="border: none; width: 33%;">
                    <span class="checkbox"></span> Transferência recebida
                </td>
                <td style="border: none; width: 34%;">
                    <span class="checkbox checked"></span> <strong>Doação</strong>
                </td>
            </tr>
        </table>
    </div>

    <div style="margin: 10px 0; padding: 5px; border: 1px solid #000;">
        <strong>Inscrição do Móvel:</strong> {{ $idBem }}
    </div>

    <div style="margin: 10px 0; padding: 5px; border: 1px solid #000;">
        <strong>Localidade de origem quando o motivo for Transferência recebida:</strong>
        <table style="margin-top: 5px;">
            <tr>
                <th style="width: 50%;">Administração</th>
                <th style="width: 30%;">Cidade</th>
                <th style="width: 20%;">Nº do Relatório</th>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </div>

    <div class="section-title">C - DADOS DO BEM</div>
    <table>
        <tr>
            <th style="width: 15%;">Tipo de bem</th>
            <th style="width: 15%;">Quantidade</th>
            <th style="width: 40%;">Descrição</th>
            <th style="width: 15%;">Valor de aquisição</th>
            <th style="width: 15%;">Local do bem</th>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>1</td>
            <td>{{ $descricaoBem }}</td>
            <td>&nbsp;</td>
            <td>{{ $dependencia }}</td>
        </tr>
        <tr>
            <td colspan="5">&nbsp;<br>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="5">&nbsp;<br>&nbsp;</td>
        </tr>
    </table>

    <div style="margin-top: 30px;">
        <table>
            <tr>
                <th style="width: 33%;">Nome</th>
                <th style="width: 33%;">Ministério</th>
                <th style="width: 34%;">Administração/Assessor</th>
            </tr>
            <tr>
                <td><strong>Colaborador Administrativo</strong><br>&nbsp;<br>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td><strong>Assinatura</strong><br>&nbsp;<br>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 20px; text-align: center;">
        <table style="border: none;">
            <tr style="border: none;">
                <td style="border: none; width: 33%; text-align: center; font-size: 9pt;">1 via - Contabilidade</td>
                <td style="border: none; width: 33%; text-align: center; font-size: 9pt;">1 via - Emissor</td>
                <td style="border: none; width: 34%; text-align: center; font-size: 9pt;">1 via - Destinatário</td>
            </tr>
        </table>
    </div>

    <div style="text-align: center; margin-top: 20px; font-size: 8pt;">
        sp.saopaulo.manualadm@congregacao.org.br
    </div>
</body>

</html>
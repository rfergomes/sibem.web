<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: sans-serif;
            line-height: 1.6;
            color: #374151;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
        }

        .header {
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .confirmado {
            background-color: #dcfce7;
            color: #15803d;
        }

        .adiado {
            background-color: #ffedd5;
            color: #c2410c;
        }

        .cancelado {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .details {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }

        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #9ca3af;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2 style="color: #1e3a8a; margin: 0;">SIBEM - Notificação de Agendamento</h2>
        </div>

        <p>Olá,</p>

        <p>Houve uma atualização no agendamento de inventário para a localidade
            <strong>{{ $appointment->local->nome }}</strong>.</p>

        <div class="status {{ $type }}">
            {{ strtoupper($type) }}
        </div>

        <div class="details">
            <p><strong>📅 Data Planejada:</strong> {{ $appointment->scheduled_at->format('d/m/Y H:i') }}</p>
            <p><strong>👤 Responsável Local:</strong> {{ $appointment->responsavel_nome }}
                ({{ $appointment->responsavel_cargo }})</p>
            @if($appointment->justification)
                <p><strong>📝 Justificativa:</strong> {{ $appointment->justification }}</p>
            @endif
        </div>

        <p>Acompanhe os detalhes no sistema SIBEM.</p>

        <div class="footer">
            SIBEM CCB - Sistema de Inventário de Bens e Móveis<br>
            Este é um e-mail automático, por favor não responda.
        </div>
    </div>
</body>

</html>
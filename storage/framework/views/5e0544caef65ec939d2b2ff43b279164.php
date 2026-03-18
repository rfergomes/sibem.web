<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagem de Contato - SIBEM</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .wrapper {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .header {
            background-color: #1a5276;
            padding: 28px 32px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 22px;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 6px 0 0;
            color: #aed6f1;
            font-size: 13px;
        }
        .body {
            padding: 32px;
        }
        .label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            color: #7f8c8d;
            letter-spacing: 0.8px;
            margin-bottom: 4px;
        }
        .value {
            font-size: 15px;
            color: #2c3e50;
            margin-bottom: 20px;
            padding: 10px 14px;
            background: #f8f9fa;
            border-left: 3px solid #1a5276;
            border-radius: 0 4px 4px 0;
        }
        .value.message-body {
            white-space: pre-wrap;
            line-height: 1.6;
        }
        .footer {
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            padding: 18px 32px;
            text-align: center;
            font-size: 12px;
            color: #95a5a6;
        }
        .footer a {
            color: #1a5276;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>📬 Nova Mensagem de Contato</h1>
            <p>Recebida pelo formulário da landing page SIBEM</p>
        </div>

        <div class="body">
            <div class="label">Nome</div>
            <div class="value"><?php echo e($contact->name); ?></div>

            <div class="label">E-mail</div>
            <div class="value"><?php echo e($contact->email); ?></div>

            <div class="label">Assunto</div>
            <div class="value"><?php echo e($contact->subject); ?></div>

            <div class="label">Mensagem</div>
            <div class="value message-body"><?php echo e($contact->message); ?></div>

            <div class="label">IP de Origem</div>
            <div class="value"><?php echo e($contact->ip_address); ?></div>

            <div class="label">Recebido em</div>
            <div class="value"><?php echo e($contact->created_at->format('d/m/Y \à\s H:i')); ?></div>
        </div>

        <div class="footer">
            <p>Este e-mail foi gerado automaticamente pelo <a href="<?php echo e(config('app.url')); ?>">SIBEM</a>.</p>
            <p>Para responder, basta responder a este e-mail — o destinatário será <strong><?php echo e($contact->email); ?></strong>.</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\sibem.web\resources\views/emails/contact/message.blade.php ENDPATH**/ ?>
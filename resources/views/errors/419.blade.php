<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Página Expirada | SIBEM Web</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/ccb-theme.css') }}">
    <style>
        .error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1a3a4a 0%, #033D60 50%, #0a5e8a 100%);
            position: relative;
            overflow: hidden;
        }

        .error-page::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -15%;
            width: 550px;
            height: 550px;
            background: rgba(255,255,255,0.04);
            border-radius: 50%;
            animation: float 9s ease-in-out infinite;
        }

        .error-page::after {
            content: '';
            position: absolute;
            bottom: -35%;
            left: -10%;
            width: 480px;
            height: 480px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
            animation: float 11s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-25px) scale(1.04); }
        }

        .error-card {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 50px 60px;
            text-align: center;
            max-width: 520px;
            width: 90%;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 10;
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .error-icon-wrapper {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #e67e22, #d35400);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 10px 30px rgba(211, 84, 0, 0.4);
        }

        .error-icon-wrapper i {
            font-size: 48px;
            color: #fff;
        }

        .error-code {
            font-size: 80px;
            font-weight: 800;
            line-height: 1;
            color: #033D60;
            letter-spacing: -2px;
            margin-bottom: 8px;
        }

        .error-title {
            font-size: 22px;
            font-weight: 700;
            color: #1a2a3a;
            margin-bottom: 12px;
        }

        .error-message {
            color: #6c757d;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .error-divider {
            height: 3px;
            width: 60px;
            background: linear-gradient(90deg, #e67e22, #d35400);
            border-radius: 2px;
            margin: 16px auto 20px;
        }

        .btn-group-error {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary-action {
            background: linear-gradient(135deg, #033D60, #0a7abf);
            color: #fff !important;
            border: none;
            padding: 12px 28px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(3, 61, 96, 0.35);
        }

        .btn-primary-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(3, 61, 96, 0.45);
            color: #fff !important;
        }

        .btn-secondary-action {
            background: transparent;
            color: #e67e22 !important;
            border: 2px solid #e67e22;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-secondary-action:hover {
            background: #e67e22;
            color: #fff !important;
            transform: translateY(-2px);
        }

        .logo-top {
            max-height: 36px;
            margin-bottom: 24px;
            opacity: 0.85;
        }

        .tip-box {
            background: #fff8f0;
            border: 1px solid #fcd9b0;
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 28px;
            text-align: left;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .tip-box i {
            color: #e67e22;
            font-size: 20px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .tip-box p {
            margin: 0;
            font-size: 13px;
            color: #7a5a30;
            line-height: 1.5;
        }
    </style>
</head>
<body data-pc-preset="preset-10" data-pc-sidebar-theme="dark" data-pc-direction="ltr" data-pc-theme="light">

    <!-- Preloader -->
    <div class="loader-bg">
        <div class="pc-loader">
            <div class="loader-fill"></div>
        </div>
    </div>

    <div class="error-page">
        <div class="error-card">

            <img src="{{ asset('assets/images/logo-dark.svg') }}" alt="SIBEM Web" class="logo-top">

            <div class="error-icon-wrapper">
                <i class="ti ti-clock-x"></i>
            </div>

            <div class="error-code">419</div>

            <div class="error-divider"></div>

            <h1 class="error-title">Página Expirada</h1>

            <p class="error-message">
                Sua sessão expirou ou o token de segurança ficou inválido.<br>
                Isso acontece após longos períodos sem uso.
            </p>

            <div class="tip-box">
                <i class="ti ti-info-circle"></i>
                <p>Volte para a página anterior e <strong>recarregue-a</strong> antes de tentar novamente. Se o problema persistir, faça login novamente.</p>
            </div>

            <div class="btn-group-error">
                <a href="javascript:history.back()" onclick="history.back(); return false;" class="btn-secondary-action">
                    <i class="ti ti-arrow-left"></i>
                    Voltar e tentar de novo
                </a>
                <a href="{{ route('login') }}" class="btn-primary-action">
                    <i class="ti ti-login"></i>
                    Ir para o Login
                </a>
            </div>

        </div>
    </div>

    <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/pcoded.js') }}"></script>
    <script>
        layout_change('light');
        preset_change("preset-10");
    </script>
</body>
</html>

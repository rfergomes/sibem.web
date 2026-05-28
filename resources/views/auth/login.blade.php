@section('title', 'Login')
@section('meta_description', 'Acesse o SIBEM Web - Sistema para Inventário de Bens Móveis da Congregação Cristã no Brasil.')
<!DOCTYPE html>
<html lang="pt-br">
<!-- [Head] start -->
<head>
    <title>Login | SIBEM Web</title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    @include('partials.pwa-meta')
    
    <!-- [Favicon] icon -->
    <link rel="icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon">
    <!-- [Google Font] -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">
    <!-- Template CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/ccb-theme.css') }}?v={{ time() }}">
</head>
<!-- [Head] end -->

<!-- [Body] Start -->
<body data-pc-preset="preset-10" data-pc-sidebar-theme="dark" data-pc-sidebar-caption="true" data-pc-direction="ltr" data-pc-theme="light">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="pc-loader">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->

    <div class="auth-main v2">
        <div class="bg-overlay bg-dark"></div>
        <div class="auth-wrapper">
            <div class="auth-sidecontent">
                <div class="text-start px-3 px-md-5">
                    <a href="/" class="d-block mt-5">
                        <img src="{{ asset('assets/images/logo-white.svg') }}" alt="logo" class="img-fluid" style="max-height: 50px;">
                    </a>
                    <p class="text-white mt-2 mt-md-4">SIBEM CCB - Sistema para Inventário de Bens Móveis. Gerenciamento unificado e controle de acesso integrado.</p>
                </div>
            </div>

            <div class="auth-form">
                <div class="card my-5 mx-3">
                    <div class="card-header bg-dark">
                        <h4 class="text-center text-white mb-0 f-w-500">Acesse o Sistema</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('login') }}" method="POST">
                            @csrf

                            <!-- Error Alert -->
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <div class="form-group mb-3">
                                <label class="form-label" for="email">E-mail</label>
                                <input type="email" name="email" class="form-control" id="email" placeholder="nome@exemplo.com" value="{{ old('email') }}" required autofocus>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label" for="password">Senha</label>
                                <input type="password" name="password" class="form-control" id="password" placeholder="Senha" required>
                            </div>

                            <div class="d-flex mt-1 justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input input-primary" type="checkbox" id="customCheckc1" name="remember" checked>
                                    <label class="form-check-label text-muted" for="customCheckc1">Lembrar-me</label>
                                </div>
                                <a href="{{ route('password.request') }}" class="text-muted">Esqueceu a senha?</a>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary">Entrar</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer border-top text-center">
                        <p class="mb-0 text-muted">© {{ date('Y') }} SIBEM CCB. Todos os direitos reservados.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Required Js -->
    <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('assets/js/pcoded.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
    <script>
        layout_change('light');
        layout_sidebar_change('dark');
        layout_header_change('dark');
        change_box_container('false');
        layout_caption_change('true');
        layout_rtl_change('false');
        preset_change("preset-1");
    </script>

    <!-- PWA Install Floating Banner -->
    <div id="pwa-install-banner" style="display: none; position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); max-width: 450px; width: calc(100% - 40px); background: #ffffff; box-shadow: 0 10px 35px rgba(0,0,0,0.15); border-radius: 12px; padding: 18px; z-index: 9999; border: 1px solid rgba(3, 61, 96, 0.1); animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
            <div style="display: flex; align-items: center;">
                <div style="width: 40px; height: 40px; background: #033D60; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0;">
                    <img src="{{ asset('assets/images/favicon.svg') }}" style="width: 24px; height: 24px;" alt="Logo">
                </div>
                <div style="text-align: left;">
                    <h5 style="margin: 0; font-size: 14px; font-weight: 700; color: #111;">Instalar SIBEM Web</h5>
                    <p style="margin: 0; font-size: 12px; color: #666;">Instale na sua tela inicial para acesso rápido.</p>
                </div>
            </div>
            <button onclick="dismissInstallBanner()" style="background: none; border: none; cursor: pointer; color: #999; padding: 4px; display: flex; align-items: center; justify-content: center;" aria-label="Fechar">
                <i class="ti ti-x" style="font-size: 18px;"></i>
            </button>
        </div>
        <div style="display: flex; gap: 8px; justify-content: flex-end;">
            <button onclick="dismissInstallBanner()" style="background: #f1f3f5; color: #495057; border: none; border-radius: 6px; padding: 8px 16px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background 0.2s;">Agora Não</button>
            <button onclick="triggerInstall()" style="background: #033D60; color: #ffffff; border: none; border-radius: 6px; padding: 8px 20px; font-size: 13px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 10px rgba(3, 61, 96, 0.2); transition: background 0.2s;">Instalar</button>
        </div>
    </div>

    <style>
        @keyframes slideUp {
            from {
                transform: translate(-50%, 100px);
                opacity: 0;
            }
            to {
                transform: translate(-50%, 0);
                opacity: 1;
            }
        }
    </style>

    <script>
        let deferredPrompt;
        const installBanner = document.getElementById('pwa-install-banner');

        window.addEventListener('beforeinstallprompt', (e) => {
            // Impedir que o Chrome mostre o prompt padrão automaticamente
            e.preventDefault();
            // Salvar o evento para ser disparado posteriormente
            deferredPrompt = e;
            // Mostrar o banner customizado de instalação
            installBanner.style.display = 'block';
        });

        function triggerInstall() {
            if (deferredPrompt) {
                // Esconder o banner
                installBanner.style.display = 'none';
                // Disparar o prompt de instalação do navegador
                deferredPrompt.prompt();
                // Aguardar a resposta do usuário
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('[SIBEM] Usuário aceitou a instalação do PWA');
                    } else {
                        console.log('[SIBEM] Usuário recusou a instalação do PWA');
                    }
                    deferredPrompt = null;
                });
            }
        }

        function dismissInstallBanner() {
            installBanner.style.display = 'none';
        }
    </script>
</body>
</html>

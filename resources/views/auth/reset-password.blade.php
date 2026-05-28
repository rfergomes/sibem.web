@section('title', 'Redefinir Senha')
@section('meta_description', 'Redefina a sua senha de acesso ao SIBEM Web - Sistema para Inventário de Bens Móveis.')
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Redefinir Senha | SIBEM Web</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    @include('partials.pwa-meta')
    <link rel="icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/ccb-theme.css') }}?v={{ time() }}">
</head>
<body data-pc-preset="preset-10" data-pc-sidebar-theme="dark" data-pc-sidebar-caption="true" data-pc-direction="ltr" data-pc-theme="light">
    <div class="loader-bg">
        <div class="pc-loader">
            <div class="loader-fill"></div>
        </div>
    </div>

    <div class="auth-main v2">
        <div class="bg-overlay bg-dark"></div>
        <div class="auth-wrapper">
            <div class="auth-sidecontent">
                <div class="text-start px-3 px-md-5">
                    <a href="/" class="d-block mt-5">
                        <img src="{{ asset('assets/images/logo-white.svg') }}" alt="logo" class="img-fluid" style="max-height: 50px;">
                    </a>
                    <p class="text-white mt-2 mt-md-4">Crie uma nova senha segura para o seu login.</p>
                </div>
            </div>

            <div class="auth-form">
                <div class="card my-5 mx-3">
                    <div class="card-header bg-dark">
                        <h4 class="text-center text-white mb-0 f-w-500">Nova Senha</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('password.update') }}" method="POST">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="form-group mb-3">
                                <label class="form-label" for="email">Confirmar E-mail</label>
                                <input type="email" name="email" class="form-control" id="email" placeholder="nome@exemplo.com" value="{{ $email ?? old('email') }}" required autofocus>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label" for="password">Nova Senha</label>
                                <input type="password" name="password" class="form-control" id="password" placeholder="Nova Senha" required>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label" for="password_confirmation">Confirmar Nova Senha</label>
                                <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Confirmar Senha" required>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary">Salvar Nova Senha</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer border-top text-center">
                        <p class="mb-0 text-muted">© {{ date('Y') }} SIBEM CCB.</p>
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
        preset_change("preset-1");
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Recuperar Senha | SIBEM Web</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}">
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
                    <p class="text-white mt-2 mt-md-4">Recupere seu acesso ao SIBEM de forma simples e rápida.</p>
                </div>
            </div>

            <div class="auth-form">
                <div class="card my-5 mx-3">
                    <div class="card-header bg-dark">
                        <h4 class="text-center text-white mb-0 f-w-500">Recuperação de Senha</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('password.email') }}" method="POST">
                            @csrf

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
                                <label class="form-label" for="email">E-mail Cadastrado</label>
                                <input type="email" name="email" class="form-control" id="email" placeholder="nome@exemplo.com" value="{{ old('email') }}" required autofocus>
                                <small class="text-muted mt-1 d-block">Enviaremos um link de redefinição para a sua caixa de entrada.</small>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary">Enviar Link de Recuperação</button>
                            </div>

                            <div class="text-center mt-3">
                                <a href="{{ route('login') }}" class="link-primary">Voltar para o Login</a>
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

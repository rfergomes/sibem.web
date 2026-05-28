@section('title', 'Página Não Encontrada')
@section('meta_description', 'A página solicitada não foi encontrada no SIBEM Web.')
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Página Não Encontrada | SIBEM Web</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    @include('partials.pwa-meta')
    
    <link rel="icon" href="{{ asset('assets/images/favicon.svg') }}?v=2" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
</head>
<body data-pc-preset="preset-1" data-pc-sidebar-theme="dark" data-pc-sidebar-caption="true" data-pc-direction="ltr" data-pc-theme="light">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="pc-loader">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->

    <div class="auth-main v1">
        <div class="auth-wrapper">
            <div class="auth-form text-center">
                <div class="card my-5 mx-auto" style="max-width: 500px;">
                    <div class="card-body">
                        <h1 class="text-primary mt-4" style="font-size: 100px; font-weight: 800; line-height: 1;">404</h1>
                        <h4 class="mb-3">Página Não Encontrada</h4>
                        <p class="text-muted mb-4">Desculpe, a página que você está procurando não existe ou foi movida.</p>
                        <a href="{{ route('dashboard') }}" class="btn btn-primary mb-4">Ir para o Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/pcoded.js') }}"></script>
    <script>
        layout_change('light');
        preset_change("preset-1");
    </script>
</body>
</html>

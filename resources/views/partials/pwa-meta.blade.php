<!-- Progressive Web App (PWA) Tags -->
<meta name="theme-color" content="#033D60">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="SIBEM CCB">
<link rel="apple-touch-icon" href="{{ asset('assets/images/icon-192x192.png') }}">
<link rel="manifest" href="{{ asset('manifest.json') }}">

<!-- Primary SEO Meta Tags -->
<meta name="description" content="@yield('meta_description', 'SIBEM CCB - Sistema para Inventário de Bens Móveis. Gerenciamento unificado e controle de acesso integrado.')">
<meta name="keywords" content="@yield('meta_keywords', 'sibem, ccb, inventario, bens moveis, congregacao cristã no brasil, gestao de bens, igreja')">
<meta name="author" content="SIBEM CCB">
<meta name="robots" content="@yield('meta_robots', 'index, follow')">

<!-- Open Graph / Facebook / WhatsApp (Social SEO) -->
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="@yield('title', 'Painel') | SIBEM Web">
<meta property="og:description" content="@yield('meta_description', 'SIBEM CCB - Sistema para Inventário de Bens Móveis. Gerenciamento unificado e controle de acesso integrado.')">
<meta property="og:image" content="@yield('og_image', asset('assets/images/icon-512x512.png'))">
<meta property="og:locale" content="pt_BR">
<meta property="og:site_name" content="SIBEM CCB">

<!-- Twitter Cards -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ url()->current() }}">
<meta name="twitter:title" content="@yield('title', 'Painel') | SIBEM Web">
<meta name="twitter:description" content="@yield('meta_description', 'SIBEM CCB - Sistema para Inventário de Bens Móveis. Gerenciamento unificado e controle de acesso integrado.')">
<meta name="twitter:image" content="@yield('og_image', asset('assets/images/icon-512x512.png'))">

<!-- Service Worker Registration Script -->
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register("{{ asset('sw.js') }}")
                .then(reg => {
                    console.log('[SIBEM] Service Worker registrado com sucesso. Escopo:', reg.scope);
                })
                .catch(err => {
                    console.error('[SIBEM] Erro ao registrar o Service Worker:', err);
                });
        });
    }
</script>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>@yield('title', 'Painel') | SIBEM Web</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">
    
    <!-- Template CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    @yield('styles')
</head>

<body data-pc-preset="preset-1" data-pc-sidebar-theme="dark" data-pc-sidebar-caption="true" data-pc-direction="ltr" data-pc-theme="light">
    
    <!-- Preloader -->
    <div class="loader-bg">
        <div class="pc-loader">
            <div class="loader-fill"></div>
        </div>
    </div>

    <!-- [ Sidebar ] start -->
    <nav class="pc-sidebar">
        <div class="navbar-wrapper">
            <div class="m-header">
                <a href="{{ route('dashboard') }}" class="b-brand text-primary">
                    <img src="{{ asset('assets/images/logo-white.svg') }}" alt="logo" class="logo-lg" style="max-height: 40px;">
                </a>
                <div class="pc-h-item">
                    <a href="#" class="pc-sidebar-collapse" id="sidebar-hide">
                        <i class="ti ti-chevrons-left"></i>
                    </a>
                </div>
            </div>
            
            <div class="navbar-content">
                <ul class="pc-navbar">
                    <li class="pc-item pc-caption">
                        <label>Navegação</label>
                    </li>
                    
                    <li class="pc-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                            <span class="pc-mtext">Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="pc-item {{ Request::routeIs('inventarios.concluidos') ? 'active' : '' }}">
                        <a href="{{ route('inventarios.concluidos') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-clipboard-list"></i></span>
                            <span class="pc-mtext">Inventários Realizados</span>
                        </a>
                    </li>

                    @if(Auth::user()->isAdminSistema() || Auth::user()->isAdminRegional() || Auth::user()->isAdminLocal())
                        <li class="pc-item pc-caption">
                            <label>Administração</label>
                        </li>

                        <li class="pc-item {{ Request::routeIs('admin.token-requests.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.token-requests.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-key"></i></span>
                                <span class="pc-mtext">Solicitações de Tokens</span>
                            </a>
                        </li>
                    @endif
                    
                    <li class="pc-item mt-4">
                        <a href="{{ route('logout') }}" class="pc-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <span class="pc-micon"><i class="ti ti-logout text-danger"></i></span>
                            <span class="pc-mtext text-danger">Sair do Sistema</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- [ Sidebar ] end -->

    <!-- [ Header ] start -->
    <header class="pc-header">
        <div class="header-wrapper">
            <div class="me-auto pc-mob-drp">
                <ul class="list-unstyled">
                    <!-- Sidebar collapse trigger -->
                    <li class="pc-h-item pc-sidebar-collapse">
                        <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="ms-auto">
                <ul class="list-unstyled d-flex align-items-center mb-0">
                    
                    <!-- Tenant Selection Dropdown Component -->
                    <li class="pc-h-item me-3">
                        @include('components.tenant-selector')
                    </li>
                    
                    <!-- Theme toggler -->
                    <li class="dropdown pc-h-item">
                        <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <i class="ti ti-settings"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
                            <a href="#!" class="dropdown-item" onclick="layout_change('light')">
                                <i class="ti ti-sun"></i><span>Modo Claro</span>
                            </a>
                            <a href="#!" class="dropdown-item" onclick="layout_change('dark')">
                                <i class="ti ti-moon"></i><span>Modo Escuro</span>
                            </a>
                        </div>
                    </li>

                    <!-- User Profile Dropdown -->
                    <li class="dropdown pc-h-item header-user-profile">
                        <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; font-weight: 600;">
                                {{ substr(Auth::user()->nome, 0, 2) }}
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
                            <div class="dropdown-header">
                                <h5 class="text-overflow mb-0">Olá, {{ Auth::user()->nome }}</h5>
                                <small class="text-muted">{{ ucfirst(str_replace('_', ' ', Auth::user()->perfil)) }}</small>
                            </div>
                            <hr class="m-0">
                            <a href="{{ route('logout') }}" class="dropdown-item text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="ti ti-power"></i><span>Sair</span>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <!-- [ Header ] end -->

    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">
            
            <!-- Toast System Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ti ti-circle-check-filled me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ti ti-circle-x-filled me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
            
        </div>
    </div>
    <!-- [ Main Content ] end -->

    <!-- Required Js -->
    <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('assets/js/pcoded.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        layout_change('light');
        layout_sidebar_change('dark');
        layout_header_change('light');
        change_box_container('false');
        preset_change("preset-1");
    </script>
    
    @yield('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>@yield('title', 'Painel') | SIBEM Web</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">
    
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
    <link rel="stylesheet" href="{{ asset('assets/css/ccb-theme.css') }}?v={{ time() }}">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    @yield('styles')
</head>

<body data-pc-preset="preset-10" data-pc-sidebar-theme="dark" data-pc-sidebar-caption="true" data-pc-direction="ltr" data-pc-theme="light" data-pc-header-theme="dark">
    
    <!-- Preloader -->
    <div class="loader-bg">
        <div class="pc-loader">
            <div class="loader-fill"></div>
        </div>
    </div>

    <!-- [ Sidebar ] start -->
    <nav class="pc-sidebar">
        <div class="navbar-wrapper" style="position: relative; height: 100%;">
            <div class="m-header">
                <a href="{{ route('dashboard') }}" class="b-brand text-primary">
                    <img src="{{ asset('assets/images/sibem_logo_claro.png') }}" alt="logo" class="logo-lg" style="max-height: 58px;">
                </a>
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
                            <label>Administração de Acesso</label>
                        </li>

                        <li class="pc-item {{ Request::routeIs('admin.usuarios.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.usuarios.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-users"></i></span>
                                <span class="pc-mtext">Usuários</span>
                            </a>
                        </li>

                        @if(Auth::user()->isAdminSistema() || Auth::user()->isAdminRegional() || Auth::user()->isAdminLocal())
                        <li class="pc-item {{ Request::routeIs('admin.tokens.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.tokens.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-key"></i></span>
                                <span class="pc-mtext">Tokens de Acesso</span>
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->isAdminSistema() || Auth::user()->isAdminRegional())
                        <li class="pc-item {{ Request::routeIs('admin.token-requests.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.token-requests.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-mail-opened"></i></span>
                                <span class="pc-mtext">Solicitações de Tokens</span>
                            </a>
                        </li>
                        @endif

                        <li class="pc-item pc-caption">
                            <label>Estrutura e Cadastros</label>
                        </li>

                        @if(Auth::user()->isAdminSistema())
                            <li class="pc-item {{ Request::routeIs('admin.regionais.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.regionais.index') }}" class="pc-link">
                                    <span class="pc-micon"><i class="ti ti-map-pin"></i></span>
                                    <span class="pc-mtext">Adm. Regionais</span>
                                </a>
                            </li>
                        @endif

                        <li class="pc-item {{ Request::routeIs('admin.locais.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.locais.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-building"></i></span>
                                <span class="pc-mtext">Adm. Locais</span>
                            </a>
                        </li>

                        <li class="pc-item {{ Request::routeIs('admin.setores.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.setores.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-chart-bar"></i></span>
                                <span class="pc-mtext">Setores</span>
                            </a>
                        </li>

                        <li class="pc-item {{ Request::routeIs('admin.dependencias.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.dependencias.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-folders"></i></span>
                                <span class="pc-mtext">Dependências</span>
                            </a>
                        </li>

                        <li class="pc-item {{ Request::routeIs('admin.igrejas.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.igrejas.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-building-community"></i></span>
                                <span class="pc-mtext">Igrejas (CCB)</span>
                            </a>
                        </li>

                        <li class="pc-item {{ Request::routeIs('admin.tipos-imovel.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.tipos-imovel.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-folder"></i></span>
                                <span class="pc-mtext">Tipos de Imóvel</span>
                            </a>
                        </li>
                    @endif
                    
                    <li class="pc-item mt-4" style="margin-bottom: 60px;">
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
            <!-- FIXED BOTTOM FOOTER -->
            <div class="pc-sidebar-footer" style="position: absolute; bottom: 0; left: 0; right: 0; padding: 12px 20px; border-top: 1px solid rgba(255, 255, 255, 0.08); background: #033D60; text-align: center; z-index: 10;">
                <button type="button" class="btn btn-sm w-100 d-flex align-items-center justify-content-center" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.15); color: #fff; font-size: 12px; font-weight: 500; border-radius: 6px; padding: 8px 12px; transition: all 0.2s;" data-bs-toggle="modal" data-bs-target="#sobreProjetoModal" onmouseover="this.style.background='rgba(255,255,255,0.1)'; this.style.borderColor='rgba(255,255,255,0.25)';" onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.borderColor='rgba(255,255,255,0.15)';">
                    <i class="ti ti-info-circle me-2" style="font-size: 15px;"></i> Sobre o SIBEM
                </button>
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
                        <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                    <li class="pc-h-item pc-sidebar-popup">
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
                            <i class="ti ti-sun"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
                            <a href="#!" class="dropdown-item" onclick="layout_change('light')">
                                <i class="ti ti-sun"></i><span>Modo Claro</span>
                            </a>
                            <a href="#!" class="dropdown-item" onclick="layout_change('dark')">
                                <i class="ti ti-moon"></i><span>Modo Escuro</span>
                            </a>
                            <a href="#!" class="dropdown-item" onclick="layout_change_default()">
                                <i class="ti ti-device-desktop"></i><span>Padrão do Sistema</span>
                            </a>
                        </div>
                    </li>

                    <!-- User Profile Dropdown -->
                    <li class="dropdown pc-h-item header-userc:\Users\Rodrigo.Lima\Downloads\sibem_logo_novo.png-profile">
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
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-sm-auto">
                            <div class="page-header-title">
                                <h5 class="mb-0">@yield('title', 'Painel')</h5>
                            </div>
                        </div>
                        <div class="col-sm-auto">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti ti-home"></i></a></li>
                                @if(Request::routeIs('admin.*'))
                                    <li class="breadcrumb-item"><a href="javascript: void(0)">Administração</a></li>
                                @elseif(!Request::routeIs('dashboard'))
                                    <li class="breadcrumb-item"><a href="javascript: void(0)">Navegação</a></li>
                                @endif
                                <li class="breadcrumb-item" aria-current="page">@yield('title', 'Painel')</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            
            <!-- Session notifications rendered dynamically via SweetAlert2 toasts at the bottom -->

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
    <script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Session Toast Alerts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4500,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: {!! json_encode(session('success')) !!}
                });
            @endif

            @if(session('error'))
                Toast.fire({
                    icon: 'error',
                    title: {!! json_encode(session('error')) !!}
                });
            @endif

            @if(session('warning'))
                Toast.fire({
                    icon: 'warning',
                    title: {!! json_encode(session('warning')) !!}
                });
            @endif

            @if(session('info'))
                Toast.fire({
                    icon: 'info',
                    title: {!! json_encode(session('info')) !!}
                });
            @endif

            @if($errors->any())
                Toast.fire({
                    icon: 'error',
                    html: {!! json_encode(implode("<br>", $errors->all())) !!}
                });
            @endif
        });
    </script>
    
    <script>
        // Inicialização global do Choices.js para selects de formulários
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('select.form-select').forEach(function (select) {
                // Pular o seletor de tenant no cabeçalho e selects com a classe no-choices
                if (select.closest('#tenant-selector-form') || select.classList.contains('no-choices')) {
                    return;
                }

                // Inicializa o Choices
                const choices = new Choices(select, {
                    allowHTML: true,
                    placeholder: true,
                    shouldSort: false,
                    searchEnabled: true,
                    removeItemButton: select.hasAttribute('multiple'),
                    itemSelectText: '',
                    noResultsText: 'Nenhum resultado encontrado',
                    noChoicesText: 'Sem opções disponíveis',
                    placeholderValue: select.getAttribute('placeholder') || (select.options[0] ? select.options[0].text : 'Selecione...'),
                    searchPlaceholderValue: 'Pesquisar...',
                });

                // Lida com erros de validação HTML5 requeridos para selects escondidos
                if (select.hasAttribute('required')) {
                    select.addEventListener('invalid', function (e) {
                        e.preventDefault();
                        const container = select.closest('.choices');
                        if (container) {
                            container.classList.add('is-invalid');
                            container.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            choices.showDropdown();
                        }
                    });
                    
                    select.addEventListener('change', function () {
                        const container = select.closest('.choices');
                        if (container && select.value) {
                            container.classList.remove('is-invalid');
                        }
                    });
                }
            });
        });

        // Configurações visuais de tema/layout
        layout_change('light');
        layout_sidebar_change('dark');
        layout_header_change('dark');
        preset_change("preset-10");
    </script>
    
    <!-- Modal Sobre o Projeto -->
    <div class="modal fade" id="sobreProjetoModal" tabindex="-1" aria-labelledby="sobreProjetoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border: none; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); overflow: hidden;">
                <div class="modal-header d-flex align-items-center justify-content-between py-3" style="background: #033D60; border-bottom: none;">
                    <h5 class="modal-title text-white fw-bold d-flex align-items-center" id="sobreProjetoModalLabel">
                        <i class="ti ti-info-circle me-2" style="font-size: 20px;"></i> Sobre
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5" style="background-color: #f4f7fa;">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-end">
                            <img src="{{ asset('assets/images/sibem_logo.png') }}" alt="logo" style="max-height: 70px;">
                            <span class="ms-3 text-muted" style="font-size: 20px; font-weight: 300; line-height: 1;">Versão 4.1</span>
                        </div>
                        <div>
                            <img src="{{ asset('assets/images/CCB_fundo_escuro.png') }}" alt="CCB" style="max-height: 80px;">
                        </div>
                    </div>
                    
                    <div style="font-size: 14px; line-height: 1.6; color: #333; text-align: justify;">
                        <p class="mb-3">Este software, SIBEM CCB - Sistema para Inventário de Bens Móveis, foi desenvolvido com dedicação e zelo para atender exclusivamente às necessidades da Congregação Cristã no Brasil, com o objetivo de proporcionar organização e gestão eficiente de seus bens móveis, sempre guiados pelos princípios cristãos de ordem e responsabilidade.</p>

                        <p class="mb-3">A versão 4 do SIBEM CCB reflete o esforço coletivo de irmãos que se dedicaram a este propósito com amor e compromisso.</p>

                        <p class="mb-3">Agradecemos a Deus por nos conceder sabedoria, força e inspiração para concluir este projeto. Nosso reconhecimento especial vai aos irmãos que contribuíram intelectualmente e tecnicamente, cuja colaboração foi fundamental para o sucesso desta versão.</p>

                        <p class="mb-3">Que este trabalho seja uma ferramenta eficaz na administração dos bens da irmandade, sempre para a glória do Senhor.</p>

                        <p class="fst-italic my-4 text-center" style="font-size: 15px; color: #555;">
                            "Tudo quanto fizerdes, fazei-o de todo o coração, como ao Senhor, e não aos homens." <br>
                            <span class="fw-bold">(Colossenses 3:23)</span>
                        </p>

                        <p class="mb-1">Vossos irmãos em Cristo,</p>
                        
                        <div class="fw-bold mt-2" style="line-height: 1.4; color: #111;">
                            Rodrigo Lima<br>
                            Jackson Passos<br>
                            Marcos Dias<br>
                            Marcos Roberto<br>
                            Emanoel Oliveira
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @yield('scripts')
</body>
</html>

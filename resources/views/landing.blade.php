@section('title', 'SIBEM')
@section('meta_description', 'Sistema desenvolvido para demanda de inventários da Congregação Cristã no Brasil.')
@section('meta_keywords', 'CCB, inventario, ativo, imobilizado, bens, moveis')
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>SIBEM - Sistema para Inventário de Bens Móveis</title>
    
    @include('partials.pwa-meta')

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('landing/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('landing/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('landing/assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('landing/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('landing/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="{{ asset('landing/assets/css/main.css') }}" rel="stylesheet">

    <style>
        /* Glassmorphism Header */
        .header {
            background: rgba(255, 255, 255, 0.85) !important;
            backdrop-filter: blur(12px) !important;
            -webkit-backdrop-filter: blur(12px) !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        /* Feature Cards Hover Interactions */
        .hero .icon-box {
            transition: all 0.35s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
            border: 1px solid rgba(0, 0, 0, 0.04) !important;
            background: #ffffff !important;
            border-radius: 10px !important;
        }

        .hero .icon-box:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 35px rgba(3, 61, 96, 0.12) !important;
            border-color: #033D60 !important;
        }

        /* Stats Widgets */
        #stats .stats-item {
            background: #ffffff;
            padding: 35px 25px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
            border-top: 4px solid #033D60;
            transition: all 0.35s cubic-bezier(0.25, 0.8, 0.25, 1);
            border-left: 1px solid rgba(0,0,0,0.02);
            border-right: 1px solid rgba(0,0,0,0.02);
            border-bottom: 1px solid rgba(0,0,0,0.02);
        }

        #stats .stats-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(3, 61, 96, 0.1);
        }

        #stats .stats-item .purecounter {
            font-size: 36px;
            font-weight: 700;
            color: #033D60;
            display: block;
            margin-bottom: 8px;
        }

        #stats .stats-item p {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: #666;
        }

        /* Version Frame Wrapper */
        .version-frame-wrapper {
            background: #ffffff;
            border-radius: 12px;
            padding: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* PWA Button Styling in Hero */
        .btn-pwa {
            font-family: var(--heading-font);
            font-weight: 600;
            font-size: 15px;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 30px;
            border-radius: 50px;
            transition: all 0.3s ease;
            margin: 10px;
            border: 2px solid #033D60;
            background: transparent;
            color: #033D60;
            text-decoration: none;
            gap: 8px;
        }

        .btn-pwa:hover {
            background: #033D60;
            color: #ffffff !important;
            box-shadow: 0 8px 20px rgba(3, 61, 96, 0.25);
            transform: translateY(-1px);
        }

        /* General style utilities */
        .mapouter {
            position: relative;
            text-align: right;
            width: 100%;
            height: 370px;
        }

        .gmap_canvas {
            overflow: hidden;
            background: none !important;
            width: 100%;
            height: 370px;
        }

        .gmap_iframe {
            height: 370px !important;
        }

        .btn-login {
            background: var(--accent-color);
            color: var(--default-color);
            padding: 8px 25px;
            margin-left: 30px;
            border-radius: 50px;
            transition: 0.3s;
            font-size: 14px;
            font-weight: 600;
            border: 2px solid var(--accent-color);
        }

        .btn-login:hover {
            background: transparent;
            color: var(--accent-color);
        }

        /* Success Message Styling */
        .php-email-form .sent-message {
            display: none;
            color: #fff;
            background: #18d26e;
            text-align: center;
            padding: 20px;
            font-weight: 500;
            border-radius: 8px;
            margin-bottom: 24px;
            box-shadow: 0 4px 15px rgba(24, 210, 110, 0.2);
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="index-page" data-pc-preset="preset-10" data-pc-sidebar-theme="dark" data-pc-sidebar-caption="true" data-pc-direction="ltr" data-pc-theme="light">

    <header id="header" class="header d-flex align-items-center sticky-top">
        <div class="container-fluid container-xl position-relative d-flex align-items-center">

            <a href="/" class="logo d-flex align-items-center me-auto">
                <img src="{{ asset('landing/assets/img/logo.png') }}" alt="Logo SIBEM" class="img-fluid">
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li style="color: var(--primary-color);">Home</li>
                    <li><a href="#sobre">Sobre</a></li>
                    <li><a href="#stats">Números</a></li>
                    <li><a href="#about-alt">Vídeo</a></li>
                    <li><a href="#versao">Versão</a></li>
                    <li><a href="#faq">Dúvidas</a></li>
                    <li><a href="#contact">Contato</a></li>
                    <li><a href="{{ asset('landing/html/Manual_SIBEM.html') }}" target="_blank">Documentação</a></li>
                    <li><a href="{{ route('login') }}" class="fw-bold text-primary">Login</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

            <a class="btn-getstarted" href="{{ asset('landing/suporte/SIBEM_Suporte.exe') }}">Suporte Remoto</a>

        </div>
    </header>

    <main class="main">

        <!-- Hero Section -->
        <section id="home" class="hero section">

            <img src="{{ asset('landing/assets/img/hero-bg-abstract.jpg') }}" alt="" data-aos="fade-in" class="">

            <div class="container">
                <div class="row justify-content-center" data-aos="zoom-out">
                    <div class="col-xl-7 col-lg-9 text-center">
                        <h1>Sistema para Inventário de Bens Móveis</h1>
                        <h4><span class="fw-bold muted">(Ativo Imobilizado)</span></h4>
                        <p>Uso exclusivo da Congregação Cristã no Brasil - CCB&copy;</p>
                    </div>
                </div>
                <div class="text-center d-flex flex-wrap justify-content-center gap-2" data-aos="zoom-out" data-aos-delay="100" style="margin-top: 20px;">
                    <a href="{{ asset('app/setup.exe') }}" class="btn-pwa"><i class="bi bi-download me-1"></i> Baixar SIBEM Desktop</a>
                    <a href="{{ route('login') }}" class="btn-pwa"><i class="bi bi-pc-display-horizontal me-1"></i> Acessar SIBEM Web</a>
                </div>

                <div class="row gy-4 mt-5">
                    <div class="col-md-6 col-lg-3" data-aos="zoom-out" data-aos-delay="100">
                        <div class="icon-box">
                            <div class="icon"><i class="bi bi-upc-scan"></i></div>
                            <h4 class="title"><a href="">Scanner Óptico</a></h4>
                            <p class="description">Utilize o scanner para leitura do código de barras e agilize o inventário.
                            </p>
                        </div>
                    </div><!--End Icon Box -->

                    <div class="col-md-6 col-lg-3" data-aos="zoom-out" data-aos-delay="200">
                        <div class="icon-box">
                            <div class="icon"><i class="bi bi-phone"></i></div>
                            <h4 class="title"><a href="">Smartphone</a></h4>
                            <p class="description">O sistema permite a coleta de dados via smartphone, otimizando o processo de inventário.</p>
                        </div>
                    </div><!--End Icon Box -->

                    <div class="col-md-6 col-lg-3" data-aos="zoom-out" data-aos-delay="300">
                        <div class="icon-box">
                            <div class="icon"><i class="bi bi-exclamation-triangle"></i></div>
                            <h4 class="title"><a href="">Pendências</a></h4>
                            <p class="description">Gerencie e resolva pendências de forma eficiente para garantir a integridade dos dados do patrimônio.</p>
                        </div>
                    </div><!--End Icon Box -->

                    <div class="col-md-6 col-lg-3" data-aos="zoom-out" data-aos-delay="400">
                        <div class="icon-box">
                            <div class="icon"><i class="bi bi-file-pdf"></i></div>
                            <h4 class="title"><a href="">Relatórios</a></h4>
                            <p class="description">Gere relatórios detalhados para auditorias e gest&atilde;o eficiente do patrim&iacute;nio.</p>
                        </div>
                    </div><!--End Icon Box -->

                </div>
            </div>

        </section><!-- /Hero Section -->

        <!-- About Section -->
        <section id="sobre" class="about section">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Sobre<br></h2>
                <p>Sistema para Inventário de Bens Móveis - SIBEM CCB&copy;</p>
            </div><!-- End Section Title -->

            <div class="container">

                <div class="row gy-4">

                    <div class="col content" data-aos="fade-up" data-aos-delay="100">
                        <p>
                            O <strong>SIBEM CCB – Sistema para Inventário de Bens Móveis</strong> – foi desenvolvido com zelo e dedicação
                            para atender de forma exclusiva às necessidades da Congregação Cristã no Brasil. Nosso objetivo é
                            proporcionar maior organização e eficiência na gestão do patrimônio mobiliário, sempre fundamentados
                            nos princípios cristãos de ordem, zelo e responsabilidade.</p>

                        <p>A versão 4 do SIBEM CCB reflete o amadurecimento deste projeto e o esforço coletivo de irmãos
                            que se dedicaram voluntariamente a este propósito com amor, comunhão e compromisso.</p>

                        <p>Agradecemos primeiramente a Deus por nos conceder a sabedoria, a força e a inspiração necessárias para
                            a realização deste trabalho. Expressamos também nosso sincero reconhecimento aos irmãos que colaboraram
                            técnica e intelectualmente, cujo empenho foi fundamental para o sucesso e desenvolvimento desta nova versão.</p>

                        <p>Que esta ferramenta continue a ser um instrumento eficaz na administração dos bens da irmandade,
                            cooperando com a boa ordem e conservação do patrimônio, sempre para a honra e glória do Senhor.</p>

                        <p class="fst-italic text-muted">
                            "Tudo quanto fizerdes, fazei-o de todo o coração, como ao Senhor, e não aos homens."
                            <br>
                            <span style="font-size: 13px; font-weight: 500;">— Colossenses 3:23</span>
                        </p>

                        <p>Vossos irmãos em Cristo,</p>

                        <ul>
                            <li><i class="bi bi-check2-circle"></i> <span>Rodrigo Lima</span></li>
                            <li><i class="bi bi-check2-circle"></i> <span>Jackson Passos</span></li>
                            <li><i class="bi bi-check2-circle"></i> <span>Marcos Dias</span></li>
                            <li><i class="bi bi-check2-circle"></i> <span>Marcos Roberto</span></li>
                            <li><i class="bi bi-check2-circle"></i> <span>Emanoel Oliveira</span></li>
                        </ul>
                    </div>
                    <p class="text-center">CCB - Administração - Campinas/SP - Setor de Patrimônio</p>
                </div>
            </div>

        </section><!-- /About Section -->

        <!-- Stats Section -->
        <section id="stats" class="stats section light-background">

            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <div class="row gy-4">

                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item text-center w-100 h-100">
                            <i class="bi bi-people" style="font-size: 32px; color: #033D60; margin-bottom: 10px; display: block;"></i>
                            <span data-purecounter-start="0" data-purecounter-end="{{ $users }}"
                                data-purecounter-duration="1" class="purecounter"></span>
                            <p>Usuários</p>
                        </div>
                    </div><!-- End Stats Item -->

                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item text-center w-100 h-100">
                            <i class="bi bi-map" style="font-size: 32px; color: #033D60; margin-bottom: 10px; display: block;"></i>
                            <span data-purecounter-start="0" data-purecounter-end="{{ $regionais }}"
                                data-purecounter-duration="1" class="purecounter"></span>
                            <p>Administrações Regionais</p>
                        </div>
                    </div><!-- End Stats Item -->

                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item text-center w-100 h-100">
                            <i class="bi bi-building" style="font-size: 32px; color: #033D60; margin-bottom: 10px; display: block;"></i>
                            <span data-purecounter-start="0" data-purecounter-end="{{ $locais }}"
                                data-purecounter-duration="1" class="purecounter"></span>
                            <p>Administrações Locais</p>
                        </div>
                    </div><!-- End Stats Item -->

                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item text-center w-100 h-100">
                            <i class="bi bi-house-door" style="font-size: 32px; color: #033D60; margin-bottom: 10px; display: block;"></i>
                            <span data-purecounter-start="0" data-purecounter-end="{{ $igrejas }}"
                                data-purecounter-duration="1" class="purecounter"></span>
                            <p>Casas de Oração</p>
                        </div>
                    </div><!-- End Stats Item -->

                </div>

            </div>

        </section><!-- /Stats Section -->

        <section id="about-alt" class="about-alt section">
            <div class="container">
                <div class="row gy-4">
                    <div class="col-lg-6 position-relative align-self-start" data-aos="fade-up" data-aos-delay="100">
                        <img src="{{ asset('landing/assets/img/about.jpg') }}" class="img-fluid" alt="">
                        <a href="https://www.youtube.com/watch?v=twSOX5SxHDA" class="glightbox pulsating-play-btn"></a>
                    </div>
                    <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="200">
                        <h3>Conheça o SIBEM em Ação</h3>
                        <p class="fst-italic">
                            Assista ao vídeo demonstrativo ao lado para conhecer a interface do sistema, bem como a dinâmica de seus principais módulos e relatórios:
                        </p>
                        <ul>
                            <li><i class="bi bi-check2-all"></i> <span><strong>PAINEL DE CONTROLE (DASHBOARD)</strong>: Visualização de gráficos estatísticos e indicadores em tempo real sobre a situação de cada inventário e o progresso dos lançamentos.</span></li>
                            <li><i class="bi bi-check2-all"></i> <span><strong>MÓDULO DE INVENTÁRIO</strong>: Área dedicada à realização prática do inventário físico (in loco), com suporte à leitura de códigos de barras via scanner óptico ou celular e apontamento de divergências.</span></li>
                            <li><i class="bi bi-check2-all"></i> <span><strong>CADASTROS ESTRUTURADOS</strong>: Gerenciamento unificado de Casas de Oração, Administrações Regionais/Locais, setores físicos, usuários autorizados e catálogo de bens móveis.</span></li>
                            <li><i class="bi bi-check2-all"></i> <span><strong>CONFIGURAÇÕES E SEGURANÇA</strong>: Controle de permissões de acesso por perfil de usuário, parametrizações gerais do sistema e definição do banco de dados operacional.</span></li>
                            <li><i class="bi bi-check2-all"></i> <span><strong>SINCRONIZAÇÃO INTELIGENTE</strong>: Tecnologia de replicação de dados em nuvem que permite o trabalho offline (mesmo sem internet na igreja) e posterior consolidação no servidor central.</span></li>
                        </ul>
                        <p>
                            O SIBEM está em constante evolução, recebendo novas funcionalidades e otimizações periodicamente para melhor atender às necessidades da irmandade.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section id="versao" class="contact section light-background">
            <div class="container section-title" data-aos="fade-up">
                <h2>Última versão publicada<br></h2>
                <p>Uma vez instalado, o sistema busca por atualizações automaticamente</p>
            </div><!-- End Section Title -->
            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="version-frame-wrapper">
                    <iframe id="tabelaVersao" src="{{ asset('app/home.html') }}" width="100%" height="600" frameborder="0"
                        title="Tabela de versões" style="border:none; overflow:hidden; border-radius: 8px;">
                        <p>Não foi possível carregar o conteúdo. Acesse diretamente
                            <a href="{{ asset('app/home.html') }}" target="_blank" rel="noopener">app/home.html</a>.
                        </p>
                    </iframe>
                </div>
            </div>
        </section>

        <!-- Faq Section -->
        <section id="faq" class="faq section ">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Perguntas frequentes</h2>
                <p>Algumas dúvidas podem ser resolvidas por aqui</p>
            </div><!-- End Section Title -->

            <div class="container">

                <div class="row justify-content-center">

                    <div class="col-lg-10" data-aos="fade-up" data-aos-delay="100">

                        <div class="faq-container">

                            <div class="faq-item faq-active">
                                <h3>O sistema atende a todo o território nacional?</h3>
                                <div class="faq-content">
                                    <p>Sim. O SIBEM foi projetado para se adaptar com facilidade à estrutura e às particularidades de qualquer regional, setor ou localidade dentro do território nacional.</p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div><!-- End Faq item-->

                            <div class="faq-item">
                                <h3>Como os dados são armazenados?</h3>
                                <div class="faq-content">
                                    <p>Os dados do inventário são registrados inicialmente em um banco de dados local no computador de trabalho. Posteriormente, eles podem ser sincronizados com um servidor em nuvem seguro, disponibilizando e unificando as informações para os demais usuários autorizados da administração.</p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div><!-- End Faq item-->

                            <div class="faq-item">
                                <h3>O sistema funciona sem acesso à Internet?</h3>
                                <div class="faq-content">
                                    <p>Sim. Sabendo que nem todas as casas de oração contam com conexão ativa à internet, o SIBEM foi desenvolvido para operar perfeitamente de forma offline. O usuário realiza a coleta e os lançamentos localmente e faz a sincronização dos dados assim que houver rede disponível.</p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div><!-- End Faq item-->

                            <div class="faq-item">
                                <h3>É possível gerenciar mais de uma administração no mesmo sistema?</h3>
                                <div class="faq-content">
                                    <p>Sim. O SIBEM conta com suporte multi-administração nativo, permitindo controlar diferentes regionais, setores e locais de forma independente e organizada dentro do mesmo ambiente.</p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div><!-- End Faq item-->

                            <div class="faq-item">
                                <h3>Qual é o custo de implantação, licença ou mensalidade?</h3>
                                <div class="faq-content">
                                    <p>O SIBEM é um software totalmente gratuito, livre de taxas de adesão, mensalidades ou custos de licenciamento. O único custo eventual fica a cargo da hospedagem do banco de dados em nuvem (caso a administração opte pela sincronização online), que é contratada e paga diretamente ao provedor escolhido.</p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div><!-- End Faq item-->

                            <div class="faq-item">
                                <h3>O software recebe atualizações de novos recursos?</h3>
                                <div class="faq-content">
                                    <p>Sim. O sistema passa por evoluções contínuas. Ao ser iniciado, o SIBEM verifica e instala atualizações automaticamente sempre que uma nova versão com melhorias ou correções for publicada na nuvem.</p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div><!-- End Faq item-->

                        </div>

                    </div><!-- End Faq Column-->

                </div>

            </div>

        </section><!-- /Faq Section -->

        <!-- Contact Section -->
        <section id="contact" class="contact section">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Contato</h2>
                <p>Se tiver interesse em utilizar este sistema na sua administração, entre em contato</p>
            </div><!-- End Section Title -->

            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="mapouter">
                    <div class="gmap_canvas"><iframe class="gmap_iframe" width="100%" frameborder="0" scrolling="no"
                            marginheight="0" marginwidth="0"
                            src="https://maps.google.com/maps?width=1278&amp;height=370&amp;hl=en&amp;q=Rua Maria Benedicta Transferetti, 90&amp;t=&amp;z=15&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe><a
                            href="https://sprunkin.com/">Sprunki Phases</a></div>

                </div><!-- End Google Maps -->

                <div class="row gy-4 mt-3">

                    <div class="col-lg-4">
                        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
                            <i class="bi bi-geo-alt flex-shrink-0"></i>
                            <div>
                                <h3>Endereço</h3>
                                <p>Rua Maria Benedicta Transferetti, 90</p>
                            </div>
                        </div><!-- End Info Item -->

                        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
                            <i class="bi bi-telephone flex-shrink-0"></i>
                            <div>
                                <h3>Telefone</h3>
                                <p>(19) 9.9442-6262</p>
                            </div>
                        </div><!-- End Info Item -->

                        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="500">
                            <i class="bi bi-envelope flex-shrink-0"></i>
                            <div>
                                <h3>Email</h3>
                                <p>contato@sibem.top</p>
                            </div>
                        </div><!-- End Info Item -->

                    </div>

                    <div class="col-lg-8">
                        <form action="{{ route('contact.store') }}" method="post" class="php-email-form"
                            data-aos="fade-up" data-aos-delay="200">
                            @csrf
                            <div class="row gy-4">

                                <div class="col-md-6">
                                    <input type="text" name="name" class="form-control" placeholder="Seu Nome"
                                        required="">
                                </div>

                                <div class="col-md-6 ">
                                    <input type="email" class="form-control" name="email" placeholder="Seu Email"
                                        required="">
                                </div>

                                <div class="col-md-12">
                                    <input type="text" class="form-control" name="subject" placeholder="Assunto"
                                        required="">
                                </div>

                                <div class="col-md-12">
                                    <textarea class="form-control" name="message" rows="6" placeholder="Mensagem"
                                        required=""></textarea>
                                </div>

                                <div class="col-md-12 text-center">
                                    <div class="loading">Enviando...</div>
                                    <div class="error-message"></div>
                                    <div class="sent-message">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        Sua mensagem foi enviada com sucesso! Recebemos seu contato e em breve nossa
                                        equipe retornará no seu e-mail. Deus abençoe.
                                    </div>

                                    <button type="submit">Enviar Mensagem</button>
                                </div>

                            </div>
                        </form>
                    </div><!-- End Contact Form -->

                </div>

            </div>

        </section><!-- /Contact Section -->


    </main>

    <footer id="footer" class="footer light-background">

        <div class="container footer-top">
            <div class="row gy-4">
                <div class="col-lg-9 col-md-12 footer-about">
                    <a href="/" class="logo d-flex align-items-center">
                        <span class="sitename">SIBEM</span>
                    </a>
                    <p>Sistema para inventário de bens móveis (Ativo Imobilizado) nas casas de oração da Congregação
                        Cristã no
                        Brasil</p>
                    <div class="social-links d-flex mt-4">
                        <a href="#home"><i class="bi bi-twitter-x"></i></a>
                        <a href="#home"><i class="bi bi-facebook"></i></a>
                        <a href="#home"><i class="bi bi-instagram"></i></a>
                        <a href="#home"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-12 footer-links text-center text-md-start">
                    <h4>Links Úteis</h4>
                    <ul>
                        <li><i class="bi bi-chevron-right"></i> <a href="#home">Home</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#sobre">Sobre</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="{{ route('login') }}">Login</a></li>
                    </ul>
                </div>
            </div>

            <div class="container copyright text-center mt-4">
                <p>© <span>Copyright</span> <strong class="px-1 sitename">SIBEM v4</strong> <span>Todos os direitos
                        reservados</span></p>
            </div>

    </footer>

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader"></div>

    <!-- Vendor JS Files -->
    <script src="{{ asset('landing/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('landing/assets/vendor/php-email-form/validate.js') }}"></script>
    <script src="{{ asset('landing/assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('landing/assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
    <script src="{{ asset('landing/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('landing/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('landing/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('landing/assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>

    <!-- Main JS File -->
    <script src="{{ asset('landing/assets/js/main.js') }}"></script>

</body>

</html>
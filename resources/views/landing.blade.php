<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>SIBEM - Sistema para Inventário de Bens Móveis</title>
    <meta name="description" content="Sistema desenvolvido para demanda de inventários da Congregação Cristã no Brasil">
    <meta name="keywords" content="CCB, inventario, ativo, imobilizado, bens, moveis">

    <!-- Favicons -->
    <link href="{{ asset('landing/assets/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('landing/assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

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
    </style>
</head>

<body class="index-page">

    <header id="header" class="header d-flex align-items-center sticky-top">
        <div class="container-fluid container-xl position-relative d-flex align-items-center">

            <a href="/" class="logo d-flex align-items-center me-auto">
                <img src="{{ asset('landing/assets/img/logo.png') }}" alt="Logo SIBEM" class="img-fluid">
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="#home" class="active">Home<br></a></li>
                    <li><a href="#sobre">Sobre</a></li>
                    <li><a href="#stats">Números</a></li>
                    <li><a href="#about-alt">Vídeo</a></li>
                    <li><a href="#versao">Versão</a></li>
                    <li><a href="#faq">Dúvidas</a></li>
                    <li><a href="#contact">Contato</a></li>
                    <li><a href="{{ asset('landing/html/Manual_SIBEM.html') }}" target="_blank">Documentação</a></li>
                    <li><a href="{{ route('login') }}" class="fw-bold text-primary">Acesse o Sistema</a></li>
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
                        <p>Uso exclusivo da Congregação Cristã no Brasil</p>
                    </div>
                </div>
                <div class="text-center" data-aos="zoom-out" data-aos-delay="100">
                    <a href="{{ asset('app/setup.exe') }}" class="btn-get-started">Download V4.0.0.23</a>
                </div>

                <div class="row gy-4 mt-5">
                    <div class="col-md-6 col-lg-3" data-aos="zoom-out" data-aos-delay="100">
                        <div class="icon-box">
                            <div class="icon"><i class="bi bi-upc-scan"></i></div>
                            <h4 class="title"><a href="">Scanner Óptico</a></h4>
                            <p class="description">Através do scanner, a conferência do ativo imobilizado se torna
                                rápido e prático.
                            </p>
                        </div>
                    </div><!--End Icon Box -->

                    <div class="col-md-6 col-lg-3" data-aos="zoom-out" data-aos-delay="200">
                        <div class="icon-box">
                            <div class="icon"><i class="bi bi-phone"></i></div>
                            <h4 class="title"><a href="">Smartphone</a></h4>
                            <p class="description">Utilize seu celular para coleta de dados e importe os dados coletados
                                diretamente
                                no sistema.</p>
                        </div>
                    </div><!--End Icon Box -->

                    <div class="col-md-6 col-lg-3" data-aos="zoom-out" data-aos-delay="300">
                        <div class="icon-box">
                            <div class="icon"><i class="bi bi-exclamation-triangle"></i></div>
                            <h4 class="title"><a href="">Pendências</a></h4>
                            <p class="description">Trate as pendências a fim de manter os dados do SIGA atualizado</p>
                        </div>
                    </div><!--End Icon Box -->

                    <div class="col-md-6 col-lg-3" data-aos="zoom-out" data-aos-delay="400">
                        <div class="icon-box">
                            <div class="icon"><i class="bi bi-file-pdf"></i></div>
                            <h4 class="title"><a href="">Relatórios</a></h4>
                            <p class="description">Gere relatórios para para futuras auditorias</p>
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
                <p>Sistema de Inventários - Ativo Imobilizado</p>
            </div><!-- End Section Title -->

            <div class="container">

                <div class="row gy-4">

                    <div class="col content" data-aos="fade-up" data-aos-delay="100">
                        <p>
                            Este software, SIBEM CCB - Sistema para Inventário de Bens Móveis, foi desenvolvido com
                            dedicação e zelo
                            para atender exclusivamente às necessidades da Congregação Cristã no Brasil, com o objetivo
                            de
                            proporcionar organização e gestão eficiente de seus bens móveis, sempre guiados pelos
                            princípios cristãos
                            de ordem e responsabilidade.</p>

                        <p>A versão 4 do SIBEM CCB reflete o esforço coletivo de irmãos que se dedicaram a este
                            propósito com amor e
                            compromisso.</p>

                        <p>Agradecemos a Deus por nos conceder sabedoria, força e inspiração para concluir este projeto.
                            Nosso
                            reconhecimento especial vai aos irmãos que contribuíram intelectualmente e tecnicamente,
                            cuja colaboração
                            foi fundamental para o sucesso desta versão.</p>

                        <p>Que este trabalho seja uma ferramenta eficaz na administração dos bens da irmandade, sempre
                            para a glória
                            do Senhor.</p>

                        <p>"Tudo quanto fizerdes, fazei-o de todo o coração, como ao Senhor, e não aos homens."
                            (Colossenses 3:23)</p>

                        <p>Vossos irmãos em Cristo,</p>

                        <ul>
                            <li><i class="bi bi-check2-circle"></i> <span>Rodrigo Lima</span></li>
                            <li><i class="bi bi-check2-circle"></i> <span>Jackson Passos</span></li>
                            <li><i class="bi bi-check2-circle"></i> <span>Marcos Dias</span></li>
                            <li><i class="bi bi-check2-circle"></i> <span>Marcos Roberto</span></li>
                            <li><i class="bi bi-check2-circle"></i> <span>Emanoel Oliveira</span></li>
                        </ul>
                    </div>
                    <p>CCB - Administração - Patrimônio - Ativo Imobilizado</p>



                </div>

            </div>

        </section><!-- /About Section -->

        <!-- Stats Section -->
        <section id="stats" class="stats section light-background">

            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <div class="row gy-4">

                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item text-center w-100 h-100">
                            <span data-purecounter-start="0" data-purecounter-end="{{ $users }}"
                                data-purecounter-duration="1" class="purecounter"></span>
                            <p>Usuários</p>
                        </div>
                    </div><!-- End Stats Item -->

                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item text-center w-100 h-100">
                            <span data-purecounter-start="0" data-purecounter-end="{{ $regionais }}"
                                data-purecounter-duration="1" class="purecounter"></span>
                            <p>Administrações Regionais</p>
                        </div>
                    </div><!-- End Stats Item -->

                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item text-center w-100 h-100">
                            <span data-purecounter-start="0" data-purecounter-end="{{ $locais }}"
                                data-purecounter-duration="1" class="purecounter"></span>
                            <p>Administrações Locais</p>
                        </div>
                    </div><!-- End Stats Item -->

                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item text-center w-100 h-100">
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
                        <h3>Vídeo demonstrativo do sistema de inventários.</h3>
                        <p class="fst-italic">
                            Este vídeo apresentará o sistema, bem como suas funcionalidades, módulos e relatórios
                            disponíveis.
                        </p>
                        <ul>
                            <li><i class="bi bi-check2-all"></i> <span>DASHBOARD - Apresenta as estatísticas dos
                                    inventários
                                    realizados.</span>
                            </li>
                            <li><i class="bi bi-check2-all"></i> <span>INVENTÁRIO - Realização efetiva do
                                    inventário</span></li>
                            <li><i class="bi bi-check2-all"></i> <span>CADASTROS - Apresenta a relação de Igrejas,
                                    Setores, Bens
                                    Móveis, etc...</span></li>
                            <li><i class="bi bi-check2-all"></i> <span>CONFIGURAÇÕES - Configurações gerais do sistema,
                                    como credenciais do
                                    banco online, permissões e parâmetros.</span></li>
                            <li><i class="bi bi-check2-all"></i> <span>SINCRONIZAR - Replicação dos dados com banco de
                                    dados em
                                    núvem, disponibilizando as informações à todos os usuários.</span></li>
                        </ul>
                        <p>
                            O sistema está em constante evolução, com novas funcionalidades e melhorias sendo
                            implementadas
                            periodicamente.
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
                <object id="tabelaVersao" type="text/html" width="100%" height="600"
                    data="{{ asset('app/home.html') }}" title="Tabela de versões">
                    <p>Não foi possível carregar o conteúdo. Acesse diretamente
                        <a href="{{ asset('app/home.html') }}" target="_blank" rel="noopener">app/home.html</a>.
                    </p>
                </object>
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
                                <h3>Abrange todo território nacional?</h3>
                                <div class="faq-content">
                                    <p>Sim. O sistema está preparado para atender a nível nacional.</p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div><!-- End Faq item-->

                            <div class="faq-item">
                                <h3>Os dados estão online?</h3>
                                <div class="faq-content">
                                    <p>Os dados são gravador em um banco de dados local, no computador/desktop e poserá
                                        ser sincronizado
                                        na núvem, disponibilizando as informações com os demais usuários.</p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div><!-- End Faq item-->

                            <div class="faq-item">
                                <h3>Trabalha somente online?</h3>
                                <div class="faq-content">
                                    <p>Não. Nem todas as casas de oração possuem acesso a internet, por isso o sistema
                                        trabalha com
                                        replicação de dados (sincronização)</p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div><!-- End Faq item-->

                            <div class="faq-item">
                                <h3>Posso trabalhar com mais de uma administração?</h3>
                                <div class="faq-content">
                                    <p>Sim, é possível. O sistema é multi-administrações.</p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div><!-- End Faq item-->

                            <div class="faq-item">
                                <h3>Qual valor da mensalidade e/ou implantação?</h3>
                                <div class="faq-content">
                                    <p>A licença do sistema é gratuito, sem mensalidades, sem custo de manutenção.
                                        Necessário apenas um
                                        banco de dados online com custo de hospedagem diretamente com provedor.</p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div><!-- End Faq item-->

                            <div class="faq-item">
                                <h3>Tem atualizações?</h3>
                                <div class="faq-content">
                                    <p>Sim. Sistema atualizado periodicamente.</p>
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
                                    <div class="sent-message">Sua mensagem foi enviada com sucesso! Deus abençoe.</div>

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
                <div class="credits">
                    Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a> Distributed by <a
                        href="https://themewagon.com">ThemeWagon</a>
                </div>
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

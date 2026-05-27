<!DOCTYPE html>
<html lang="pt-br">
<!-- [Head] start -->
<head>
    <title>Login | SIBEM Web</title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="SIBEM Web - Login">
    
    <!-- [Favicon] icon -->
    <link rel="icon" href="<?php echo e(asset('assets/images/favicon.svg')); ?>" type="image/x-icon">
    <!-- [Google Font] -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/tabler-icons.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/feather.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/fontawesome.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/material.css')); ?>">
    <!-- Template CSS Files -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>" id="main-style-link">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style-preset.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/ccb-theme.css')); ?>?v=<?php echo e(time()); ?>">
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
                        <img src="<?php echo e(asset('assets/images/logo-white.svg')); ?>" alt="logo" class="img-fluid" style="max-height: 50px;">
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
                        <form action="<?php echo e(route('login')); ?>" method="POST">
                            <?php echo csrf_field(); ?>

                            <!-- Error Alert -->
                            <?php if($errors->any()): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($error); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <?php if(session('success')): ?>
                                <div class="alert alert-success">
                                    <?php echo e(session('success')); ?>

                                </div>
                            <?php endif; ?>

                            <div class="form-group mb-3">
                                <label class="form-label" for="email">E-mail</label>
                                <input type="email" name="email" class="form-control" id="email" placeholder="nome@exemplo.com" value="<?php echo e(old('email')); ?>" required autofocus>
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
                                <a href="<?php echo e(route('password.request')); ?>" class="text-muted">Esqueceu a senha?</a>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary">Entrar</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer border-top text-center">
                        <p class="mb-0 text-muted">© <?php echo e(date('Y')); ?> SIBEM CCB. Todos os direitos reservados.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Required Js -->
    <script src="<?php echo e(asset('assets/js/plugins/popper.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/plugins/simplebar.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/plugins/bootstrap.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/fonts/custom-font.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/pcoded.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/plugins/feather.min.js')); ?>"></script>
    <script>
        layout_change('light');
        layout_sidebar_change('dark');
        layout_header_change('dark');
        change_box_container('false');
        layout_caption_change('true');
        layout_rtl_change('false');
        preset_change("preset-1");
    </script>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\sibem.web\resources\views/auth/login.blade.php ENDPATH**/ ?>
<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('title'); ?> - SIBEM</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div
        class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden transform hover:scale-[1.01] transition-transform duration-300">
        <!-- Header / Logo -->
        <div class="bg-ccb-blue p-8 flex justify-center">
            <img src="<?php echo e(asset('img/SIBEM_Logo_Branco.png')); ?>" alt="SIBEM Logo" class="h-16 object-contain">
        </div>

        <!-- Content -->
        <div class="p-8 text-center">
            <div class="mb-6 flex justify-center">
                <?php echo $__env->yieldContent('icon'); ?>
            </div>

            <h1 class="text-2xl font-bold text-gray-800 mb-2">
                <?php echo $__env->yieldContent('code'); ?>
            </h1>

            <h2 class="text-lg font-semibold text-blue-600 mb-4 uppercase tracking-wide">
                <?php echo $__env->yieldContent('message'); ?>
            </h2>

            <p class="text-gray-500 mb-8 leading-relaxed text-sm">
                <?php echo $__env->yieldContent('description'); ?>
            </p>

            <div class="flex justify-center flex-col gap-3">
                <?php echo $__env->yieldContent('actions'); ?>

                <?php if (! (View::hasSection('no-home-button'))): ?>
                    <a href="<?php echo e(url('/')); ?>"
                        class="text-sm text-gray-400 hover:text-gray-600 underline decoration-gray-300">
                        Voltar para o Início
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Footer Strip -->
        <div class="bg-gray-50 p-4 border-t border-gray-100 text-center">
            <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">SIBEM System</p>
        </div>
    </div>
</body>

</html>
<?php /**PATH D:\xampp\htdocs\sibem.web\resources\views/layouts/error.blade.php ENDPATH**/ ?>
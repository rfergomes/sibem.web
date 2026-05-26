

<?php $__env->startSection('title', 'Página Não Encontrada'); ?>

<?php $__env->startSection('code', '404'); ?>

<?php $__env->startSection('message', 'Página Não Encontrada'); ?>

<?php $__env->startSection('description'); ?>
    O recurso que você está procurando pode ter sido removido, ter seu nome alterado ou estar temporariamente indisponível.
<?php $__env->stopSection(); ?>

<?php $__env->startSection('actions'); ?>
    <a href="<?php echo e(url('/')); ?>"
        class="inline-block px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:text-lg">
        Voltar para a Página Inicial
    </a>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.error', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\sibem.web\resources\views/errors/404.blade.php ENDPATH**/ ?>
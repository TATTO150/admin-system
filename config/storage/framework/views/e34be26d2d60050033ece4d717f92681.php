<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Laravel')); ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <!-- Styles -->
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>


    <style>
        .background-image {
            background-image: url('https://images.pexels.com/photos/10410019/pexels-photo-10410019.jpeg');
            background-size: cover;
            background-position: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2; /* Fondo detrás de todo */
        }

        .overlay {
            background-color: rgba(0, 0, 0, 0.5);
            position: fixed; /* Mantener el overlay fijo en pantalla completa */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1; /* Justo encima del fondo */
        }

        .content-container {
            z-index: 10;
            background-color: rgba(0, 0, 0, 0.7); /* Fondo negro más oscuro semitransparente */
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3); /* Sombra suave para darle profundidad */
        }

        /* Mejorando la legibilidad del texto */
        .content-container h1, 
        .content-container p {
            color: #ffffff;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.7); /* Sombra al texto para mejorar legibilidad */
        }

        /* Responsividad para pantallas pequeñas */
        @media (max-width: 640px) {
            .content-container {
                max-width: 90%;
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="background-image"></div>
    <div class="overlay"></div>
    <div class="font-sans text-gray-900 dark:text-gray-100 antialiased min-h-screen flex items-center justify-center">
        <div class="content-container w-full max-w-xl">
            <?php if(Laravel\Fortify\Features::canManageTwoFactorAuthentication()): ?>
                <div class="mt-10 sm:mt-0">
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('profile.two-factor-authentication-form');

$__html = app('livewire')->mount($__name, $__params, 'lw-1648486415-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                </div>
                <?php if (isset($component)) { $__componentOriginal7b32e2c8c86a088fa824ad9246edeeea = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7b32e2c8c86a088fa824ad9246edeeea = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.section-border','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('section-border'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7b32e2c8c86a088fa824ad9246edeeea)): ?>
<?php $attributes = $__attributesOriginal7b32e2c8c86a088fa824ad9246edeeea; ?>
<?php unset($__attributesOriginal7b32e2c8c86a088fa824ad9246edeeea); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7b32e2c8c86a088fa824ad9246edeeea)): ?>
<?php $component = $__componentOriginal7b32e2c8c86a088fa824ad9246edeeea; ?>
<?php unset($__componentOriginal7b32e2c8c86a088fa824ad9246edeeea); ?>
<?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html>

<?php /**PATH C:\Users\carri\OneDrive\Escritorio\proyect\admin-system\resources\views/profile/show.blade.php ENDPATH**/ ?>
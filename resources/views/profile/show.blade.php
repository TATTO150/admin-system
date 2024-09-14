<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles

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
            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.two-factor-authentication-form')
                </div>
                <x-section-border />
            @endif
        </div>
    </div>

    @livewireScripts
</body>
</html>


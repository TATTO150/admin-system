<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Tu cuenta ha sido bloqueada debido a múltiples intentos fallidos de inicio de sesión. Por favor, contacta al administrador para desbloquear tu cuenta.
        </div>

        <div class="mt-4 flex items-center justify-center">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('otp.show') }}">
                {{ __('O RESETEA TU CONTRASENA PARA OBTENER ACCESO AL SISTEMA') }}
            </a>
        </div>
    </x-authentication-card>
</x-guest-layout>

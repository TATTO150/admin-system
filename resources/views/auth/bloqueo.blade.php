<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-big text-white dark:text-white-400 text-center">
            Tu cuenta ha sido bloqueada debido a múltiples intentos fallidos de inicio de sesión. Por favor, contacta al administrador para desbloquear tu cuenta.
        </div>

        <div class="mt-6 flex text-center items-center justify-center">
            <a class="ms-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150" href="{{ route('otp.show') }}">
                {{ __('O RESETEA TU CONTRASENA PARA OBTENER ACCESO AL SISTEMA') }}
            </a>
        </div>
    </x-authentication-card>
</x-guest-layout>

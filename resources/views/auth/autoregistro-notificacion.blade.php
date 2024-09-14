<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Gracias por registrarte con nosotros. En breves, un administrador te dará acceso al sistema para que puedas iniciar sesión.') }}
        </div>

        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 ms-2">
                {{ __('Volver al menu principal') }}
            </button>
        </form>
    </x-authentication-card>
</x-guest-layout>

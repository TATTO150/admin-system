<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mt-4 mb-4 text-m text-white dark:text-white text-center">
            {{ __('Gracias por registrarte con nosotros. Contacta con un adminitrador para que te conceda acceso al sistema y puedas iniciar sesión.') }}
        </div>

        <div class="flex justify-center mt-8">
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf

            <button type="submit" class="underline text-m text-white dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 ms-2">
                {{ __('Volver al menu principal') }}
            </button>
        </form>
    </div>
    </x-authentication-card>
</x-guest-layout>

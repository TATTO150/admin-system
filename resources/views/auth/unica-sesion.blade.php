<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-lg text-white dark:text-white-400 text-center">
            Ya tienes una sesión activa en el sistema. Por favor, cierra la sesión actual antes de intentar iniciar una nueva.
        </div>

        <div class="mt-6 flex text-center items-center justify-center">
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
    
                <button type="submit" class="underline text-m text-white dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 ms-2">
                    {{ __('CERRAR SESION ACTUAL') }}
                </button>
            </form>
        </div>
    </x-authentication-card>
</x-guest-layout>

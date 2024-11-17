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
    
                <button type="submit" 
                class="ms-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    {{ __('CLICK AQUÍ PARA CERRAR LAS DEMÁS SESIONES E INGRESA NUEVAMENTE') }}
                </button>

            </form>

            
        </div>
    </x-authentication-card>
</x-guest-layout>

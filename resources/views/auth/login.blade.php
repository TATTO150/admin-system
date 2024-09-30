<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="Correo_Electronico" value="{{ __('Usuario o Correo Electronico') }}" />
                <x-input id="Correo_Electronico" class="block mt-1 w-full" type="text" name="Correo_Electronico" value="" required autofocus autocomplete="off" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Contraseña') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="off" />
            </div>

            <div class="flex justify-between mt-4 items-center w-full">
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('otp.show') }}">
                    {{ __('¿Olvido su contraseña?') }}
                </a>
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('register') }}">
                    {{ __('¿No tiene una cuenta?') }}
                </a>
            </div>

            <div class="flex justify-between mt-4 items-center w-full">
                <a href="/" class="ms-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    {{ __('Cancelar') }}
                </a>
                <x-button class="ms-4">
                    {{ __('Log in') }}
                </x-button>
            </div>
            
        </form>
    </x-authentication-card>
</x-guest-layout>

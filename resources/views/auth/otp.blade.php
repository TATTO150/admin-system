<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div>
            <div class="mb-4 mt-4 text-big text-white dark:text-white text-center">
                {{ __('Para restablecer su contraseña, complete los siguientes campos. Si no cuenta con su codigo OTP, contacte con un administrador para que reestablezca su contraseña') }}
            </div>

            <x-validation-errors class="mb-4" />

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <div>
                    <x-label for="email" value="{{ __('Correo Electrónico') }}" />
                    <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                </div>

                <div class="mt-4">
                    <x-label for="password" value="{{ __('Nueva Contraseña') }}" />
                    <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                </div>

                <div class="mt-4">
                    <x-label for="password_confirmation" value="{{ __('Confirmar Nueva Contraseña') }}" />
                    <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                </div>

                <div class="mt-4">
                    <x-label for="code" value="{{ __('Código OTP') }}" />
                    <x-input id="code" class="block mt-1 w-full" type="text" name="code" required inputmode="numeric" autocomplete="one-time-code" />
                </div>

                <div class="flex justify-between mt-4 items-center w-full">
                    <a href="{{ route('login') }}" class="ms-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Cancelar') }}
                    </a>

                    <x-button>
                        {{ __('Restablecer Contraseña') }}
                    </x-button>
                </div>
            </form>
        </div>
    </x-authentication-card>
</x-guest-layout>

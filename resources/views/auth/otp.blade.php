<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div>
            <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Para restablecer su contraseña, complete los siguientes campos.') }}
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

                <div class="flex items-center justify-end mt-4">
                    <x-button>
                        {{ __('Restablecer Contraseña') }}
                    </x-button>
                </div>
            </form>
        </div>
    </x-authentication-card>
</x-guest-layout>

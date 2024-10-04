<!-- resources/views/auth/reset_password_request.blade.php -->
<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-center">
            <p class="text-lg text-white">
                {{ __('Para resetear tu contraseña, ingresa tu correo electrónico y te enviaremos un enlace para resetearla.') }}
            </p>
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.send_link') }}">
            @csrf

            <div>
                <x-label for="Correo_Electronico" value="{{ __('Correo Electrónico') }}" />
                <x-input id="Correo_Electronico" class="block mt-1 w-full" type="email" name="Correo_Electronico" :value="old('Correo_Electronico')" required autofocus />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Enviar Enlace') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>

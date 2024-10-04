<!-- resources/views/auth/reset_password_form.blade.php -->
<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-center">
            <p class="text-lg text-white">
                {{ __('Restablece tu contraseña ingresando una nueva a continuación.') }}
            </p>
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('contra.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <x-label for="Correo_Electronico" value="{{ __('Correo Electrónico') }}" />
                <x-input id="Correo_Electronico" class="block mt-1 w-full" type="email" name="Correo_Electronico" :value="old('Correo_Electronico')" required autofocus />
            </div>

            <div class="mt-4">
                <x-label for="Contrasena" value="{{ __('Nueva Contraseña') }}" />
                <x-input id="Contrasena" class="block mt-1 w-full" type="password" name="Contrasena" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="Contrasena_confirmation" value="{{ __('Confirmar Nueva Contraseña') }}" />
                <x-input id="Contrasena_confirmation" class="block mt-1 w-full" type="password" name="Contrasena_confirmation" required />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Restablecer Contraseña') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>

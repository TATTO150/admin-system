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

            <!-- Campo de nueva contraseña con ícono de ojo a la derecha -->
            <div class="mt-4">
                <x-label for="Contrasena" value="{{ __('Nueva Contraseña') }}" />
                <div class="relative">
                    <x-input id="Contrasena" class="block mt-1 w-full pr-10" type="password" name="Contrasena" required autocomplete="new-password" />
                    <span toggle="#Contrasena" class="fa fa-fw fa-eye toggle-password absolute inset-y-0 right-3 top-1/2 transform -translate-y-1/2 cursor-pointer"></span>
                </div>
            </div>

            <!-- Campo de confirmación de nueva contraseña con ícono de ojo a la derecha -->
            <div class="mt-4">
                <x-label for="Contrasena_confirmation" value="{{ __('Confirmar Nueva Contraseña') }}" />
                <div class="relative">
                    <x-input id="Contrasena_confirmation" class="block mt-1 w-full pr-10" type="password" name="Contrasena_confirmation" required />
                    <span toggle="#Contrasena_confirmation" class="fa fa-fw fa-eye toggle-password absolute inset-y-0 right-3 top-1/2 transform -translate-y-1/2 cursor-pointer"></span>
                </div>
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Restablecer Contraseña') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>

    <!-- Script para mostrar/ocultar la contraseña -->
    <script>
        document.querySelectorAll('.toggle-password').forEach(item => {
            item.addEventListener('click', function() {
                let input = document.querySelector(this.getAttribute('toggle'));
                if (input.getAttribute('type') === 'password') {
                    input.setAttribute('type', 'text');
                    this.classList.remove('fa-eye');
                    this.classList.add('fa-eye-slash');
                } else {
                    input.setAttribute('type', 'password');
                    this.classList.remove('fa-eye-slash');
                    this.classList.add('fa-eye');
                }
            });
        });
    </script>
</x-guest-layout>

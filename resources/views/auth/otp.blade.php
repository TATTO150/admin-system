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
                    <x-label for="email" value="{{ __('Usuario o Correo Electrónico') }}" />
                    <x-input id="email" class="block mt-1 w-full" type="text" name="email" :value="old('email')" required autofocus />
                </div>

                <div class="mt-4">
                    <x-label for="password" value="{{ __('Nueva Contraseña') }}" />
                    <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                    <div class="flex justify-end">
                        <button id="copy-password" class="underline mt-4 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" type="button">
                            Copiar Contraseña
                        </button>
                    </div>
                </div>
                
                <script>
                    document.getElementById('copy-password').addEventListener('click', function() {
                        const passwordInput = document.getElementById('password');
                
                        // Verifica si el navegador soporta la API del portapapeles
                        if (navigator.clipboard) {
                            // Copia el valor del campo de entrada al portapapeles
                            navigator.clipboard.writeText(passwordInput.value)
                                .then(() => {
                                    // Muestra un mensaje de confirmación
                                    alert('Contraseña copiada al portapapeles.');
                                })
                                .catch(err => {
                                    console.error('Error al copiar: ', err);
                                });
                        } else {
                            // Si la API no es soportada, muestra un mensaje
                            alert('La funcionalidad de copiar al portapapeles no es soportada en este navegador.');
                        }
                    });
                </script>
                

                <div class="mt-4">
                    <x-label for="password_confirmation" value="{{ __('Confirmar Nueva Contraseña') }}" />
                    <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                </div>

                <div class="mt-4">
                    <x-label for="code" value="{{ __('Código OTP') }}" />
                    <x-input id="code" class="block mt-1 w-full" type="text" name="code" required inputmode="numeric" autocomplete="one-time-code" />
                </div>

                <a class="underline mt-4 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('solicitar-nueva-contrasena') }}">
                    {{ __('¿No tienes tu código OTP?') }}
                </a>

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

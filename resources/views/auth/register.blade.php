<x-guest-layout>
    
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-label for="Nombre_Usuario" value="{{ __('Nombre') }}" />
                <x-input id="Nombre_Usuario" class="block mt-1 w-full" type="text" name="Nombre_Usuario" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div>
                <x-label for="Usuario" value="{{ __('Usuario') }}" />
                <x-input id="Usuario" class="block mt-1 w-full" type="text" name="Usuario" :value="old('usuario')" required autocomplete="usuario" />
            </div>

            <div class="mt-4">
                <x-label for="Contrasena" value="{{ __('Contraseña') }}" />
                <x-input id="Contrasena" class="block mt-1 w-full" type="password" name="Contrasena" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="Contrasena_confirmation" value="{{ __('Confirmar contraseña') }}" />
                <x-input id="Contrasena_confirmation" class="block mt-1 w-full" type="password" name="Contrasena_confirmation" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="Correo_Electronico" value="{{ __('Correo Electronico') }}" />
                <x-input id="Correo_Electronico" class="block mt-1 w-full" type="email" name="Correo_Electronico" :value="old('email')" required autocomplete="email" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('Acepto los :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                    {{ __('¿Ya tiene una cuenta?') }}
                </a>

                <x-button class="ms-4">
                    {{ __('Registrar') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>

</x-guest-layout>
